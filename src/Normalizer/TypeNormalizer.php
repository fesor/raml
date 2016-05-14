<?php

namespace Fesor\RAML\Normalizer;

use function Fesor\RAML\onlyWithinKeys;

class TypeNormalizer implements Normalizer
{
    public function normalize($value)
    {
        $expandedValue = is_array($value) ? $value : [];
        $expandedValue['type'] = $this->guessType($value);

        if ($this->isUserDefinedTypeOrExpression($expandedValue['type'])) {
            $expandedType = $this->expandTypeExpression($expandedValue['type']);
            if (isset($expandedType['items'])) {
                $expandedValue['type'] = 'array';
                $expandedValue['items'] = $expandedType['items'];
            } else if ($expandedType['oneOf']) {
                unset($expandedValue['type']);
                $expandedValue['oneOf'] = $expandedType['oneOf'];
            }
        }

        if (isset($expandedValue['type'])) {
            if ($expandedValue['type'] === 'object' && isset($value['properties'])) {
                $expandedValue['patternProperties'] = $this->normalizePatternProperties($value['properties']);
                $expandedValue['properties'] = $this->normalizeProperties($value['properties']);
            }
            if ($expandedValue['type'] === 'array' && isset($value['items']) && is_string($value['items'])) {
                $expandedValue['items'] = $this->expandTypeExpression($value['items']);
            }
        }
        if (isset($value['facets'])) {
            $expandedValue['facets'] = $this->normalizeProperties($value['facets']);
        }

        return array_filter($expandedValue, function ($value) {
            return null !== $value;
        });
    }

    private function isUserDefinedTypeOrExpression($type)
    {
        return $type && !in_array($type, ['string', 'number', 'integer', 'array', 'object', 'boolean', 'null']);
    }

    private function normalizePatternProperties(array $properties)
    {
        $patternProperties = $this->filterPatternProperties($properties);
        if (empty($patternProperties)) {
            return null;
        }

        return $this->expandProperties(array_combine(
            array_map(function ($key) {
                return trim($key, '/');
            }, array_keys($patternProperties)),
            $patternProperties
        ));
    }

    private function normalizeProperties(array $properties)
    {
        return $this->expandProperties(
            $this->filterPatternProperties($properties, true)
        );
    }

    private function filterPatternProperties(array $properties, $excludePatternProperties = false)
    {
        $keys = array_filter(array_keys($properties), function ($key) use ($excludePatternProperties) {
            return $excludePatternProperties ^ (!!preg_match('/^\/.*\/$/', $key));
        });

        return onlyWithinKeys($properties, $keys);
    }

    private function expandProperties(array $properties)
    {
        foreach ($properties as $prop => $definition) {
            if (is_string($definition)) {
                $properties[$prop] = $this->expandTypeExpression($properties[$prop]);
            }

            $properties[$prop] = $this->normalize($properties[$prop]);

            if (array_key_exists('required', $properties[$prop]) && $properties[$prop]['required']) {
                continue;
            }

            if (mb_substr($prop, -1) === '?') {
                $expandedProp = mb_substr($prop, 0, -1);
                $properties[$expandedProp] = $properties[$prop];
                $properties[$expandedProp]['required'] = false;
                unset($properties[$prop]);
            }
        }

        return $properties;
    }

    private function guessType($value)
    {
        if (!is_array($value)) {
            $value = ['type' => $value];
        }

        $typesForProperties = [
            'properties' => 'object',
            'items' => 'array',
            'oneOf' => null
        ];

        foreach ($typesForProperties as $prop => $type) {
            if (isset($value[$prop])) {
                return $type;
            }
        }

        if (array_key_exists('type', $value)) {
            return $value['type'] ?: 'null';
        }

        return 'string';
    }

    /**
     * Expands type expressions to json-schema like structure
     *
     * @param string $type
     * @return array
     */
    private function expandTypeExpression($type)
    {
        $rpn = $this->rpn($type);

        $stack = [];
        foreach ($rpn as $token) {
            switch ($token) {
                case '|':
                    $operands = [array_pop($stack), array_pop($stack)];;
                    if (isset($operands[0]['oneOf'])) {
                        $operands[0]['oneOf'][] = $operands[1];
                        $union = $operands[0];
                    } else {
                        $union = [
                            'oneOf' => $operands
                        ];
                    }
                    $union['oneOf'] = array_unique($union['oneOf'], SORT_REGULAR);
                    $stack[] = $union;
                    break;

                case '?':
                    $stack[] = [
                        'oneOf' => [
                            ['type' => 'null'],
                            array_pop($stack)
                        ]
                    ];
                    break;
                case '[]':
                    $stack[] = [
                        'type' => 'array',
                        'items' => array_pop($stack)
                    ];
                    break;
                default:
                    $stack[] = ['type' => $token];
                    break;
            }
        }

        return $stack[0];
    }

    /**
     * Parses type expression to RPN
     *
     * @param string $str
     * @return array
     */
    private function rpn($str)
    {
        preg_match_all('/((\[\]){1}|[\(\)\|\?]|[^\(\)\|\?\[\]\s]+?)/U', $str, $matches);
        $tokens = $matches[0];
        $stack = [];
        $out = [];
        $operators = array_flip(['|', '[]', '?']);

        foreach ($tokens as $token) {
            switch ($token) {
                case '|':
                case '?':
                case '[]':
                    while (!empty($stack) && array_key_exists(end($stack), $operators)) {
                        if ($operators[$token] < $operators[end($stack)]) {
                            $out[] = array_pop($stack);
                            continue;
                        }
                        break;
                    }
                    $stack[] = $token;
                    break;
                case '(':
                    $stack[] = $token;
                    break;
                case ')':
                    while (!empty($stack) && end($stack) !== '(') {
                        $out[] = array_pop($stack);
                    }
                    array_pop($stack);
                    break;
                default:
                    $out[] = $token;
                    break;
            }
        }

        return array_merge($out, array_reverse($stack));
    }
}
