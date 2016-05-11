<?php

namespace Fesor\RAML\Normalizer;

class TypeNormalizer
{
    public function normalize($value)
    {
        $value['type'] = $this->guessType($value);
        $value = array_merge(
            $value,
            $this->expandShortArrayTypeDeclaration($value['type'])
        );
        if ($value['type'] === 'object' && isset($value['properties'])) {
            $value['properties'] = $this->expandProperties($value['properties']);
        }
        if ($value['type'] === 'array' && isset($value['items'])) {
            $value['items'] = $this->expandItems($value['items']);
        }

        return $value;
    }

    private function expandItems($items)
    {
        if (!is_array($items)) {
            return $items;
        }

        return $this->normalize($items);
    }

    private function expandProperties(array $properties)
    {
        foreach ($properties as $prop => $definition) {
            if (is_string($definition)) {
                $properties[$prop] = ['type' => $definition];
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

    private function expandShortArrayTypeDeclaration($type)
    {
        if (mb_substr($type, -2) === '[]') {
            return [
                'type' => 'array',
                'items' => mb_substr($type, 0, -2)
            ];
        }

        return [];
    }

    private function guessType($value)
    {
        if (isset($value['type'])) {
            return $value['type'];
        }

        $typesForProperties = [
            'properties' => 'object',
            'items' => 'array'
        ];

        foreach ($typesForProperties as $prop => $type) {
            if (isset($value[$prop])) {
                return $type;
            }
        }

        return 'string';
    }

    private function getKnowPropertiesForType($type)
    {
        $base = ['default', 'type', 'example', 'examples', 'displayName', 'description', 'facets', 'xml'];
        $byTypes = [
            'object' => [
                'properties', 'minProperties', 'maxProperties', 'additionalProperties',
                'discriminator', 'discriminatorValue'
            ],
            'scalar' => ['enum'],
            'string' => ['pattern', 'minLength', 'maxLength'],
            'file' => ['fileTypes', 'minLength', 'maxLength'],
            'number' => ['minimum', 'maximum', 'format', 'multipleOf'],
            'array' => ['uniqueItems', 'items', 'minItems', 'maxItems'],
            'datetime' => ['format']
        ];
        $byTypes['integer'] = $byTypes['number'];
    }

    private function getTypeChain()
    {

    }
}
