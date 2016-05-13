<?php

namespace Fesor\RAML\Normalizer;

class TypeNormalizer implements Normalizer
{

    public function normalize($value)
    {
        $type = $value['type'] = $this->guessType($value);

        if ($this->isUserDefinedTypeOrExpression($value['type'])) {
            $expandedType = $this->expandTypeExpression($value['type']);
            if (isset($expandedType['items'])) {
                $value['type'] = 'array';
                $value['items'] = $expandedType['items'];
            } else if ($expandedType['oneOf']) {
                unset($value['type']);
                $value['oneOf'] = $expandedType['oneOf'];
                $type = 'union';
            }
        }
        if ($type === 'object' && isset($value['properties'])) {
            $value['properties'] = $this->expandProperties($value['properties']);
        }
        if ($type === 'array' && isset($value['items']) && is_string($value['items'])) {
            $value['items'] = $this->expandTypeExpression($value['items']);
        }

        return array_filter($value);
    }

    private function isUserDefinedTypeOrExpression($type)
    {
        return $type && !in_array($type, ['string', 'number', 'integer', 'array', 'object', 'boolean']);
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

        return isset($value['type']) ? $value['type'] : 'string';
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
        preg_match_all('/((\[\]){1}|[\(\)\|]|[^\(\)\|\[\]\s]+?)/U', $str, $matches);
        $tokens = $matches[0];
        $stack = [];
        $out = [];
        $operators = array_flip(['|', '[]']);

        foreach ($tokens as $token) {
            switch ($token) {
                case '|':
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
