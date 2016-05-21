<?php

namespace Fesor\RAML\Type;

class TypeRegistry implements TypeResolver
{
    private $types = [];

    private static $typeDefinitionClassMap = [
        'string' => StringType::class,
        'number' => NumberType::class,
        'boolean' => ScalarType::class,
        'null' => ScalarType::class,
        'object' => ObjectType::class
    ];

    public function register($name, $typeDefinition = null)
    {
        if (!$typeDefinition instanceof Type) {
            $typeDefinition = $this->createTypeFromDefinition($name, $typeDefinition);
        }

        $this->types[$name] = $typeDefinition;
    }

    public function isTypeRegistered($name)
    {
        return array_key_exists($name, $this->types);
    }

    /**
     * @param $typeName
     * @return Type
     */
    public function resolve($typeName)
    {
        if (array_key_exists($typeName, $this->types)) {
            return $this->types[$typeName];
        }

        throw new \RuntimeException(sprintf(
            'Unable to resolve type "%s"', $typeName
        ));
    }

    private function createTypeFromDefinition($name, array $typeDefinition)
    {
        if (!isset($typeDefinition['type']) && isset($typeDefinition['oneOf'])) {
            // todo: union types
            throw new \RuntimeException('Union types are not implemented');
        }

        if (!isset($typeDefinition['type'])) {
            throw new \RuntimeException('Unable to register unknown type');
        }

        $parent = null;
        if (!isset(self::$typeDefinitionClassMap[$typeDefinition['type']])) {
            $parent = $this->resolve($typeDefinition['type']);
            $className = get_class($parent);
        } else {
            $className = self::$typeDefinitionClassMap[$typeDefinition['type']];
        }

        return new $className($name, $typeDefinition, $parent);
    }
}
