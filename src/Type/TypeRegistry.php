<?php

namespace Fesor\RAML\Type;

class TypeRegistry
{
    private $types = [];

    private static $typeDefinitionClassMap = [
        'string' => StringType::class,
        'number' => NumberType::class,
        'boolean' => ScalarType::class,
        'null' => ScalarType::class,
        'object' => ObjectType::class
    ];

    public function __construct()
    {
        $this->register('integer', [
            'type' => 'number',
            'multipleOf' => 1
        ]);
    }

    public function register($name, $typeDefinition = null)
    {
        if (!$typeDefinition instanceof Type) {
            $typeDefinition = $this->createTypeFromDefinition($name, $typeDefinition);
        }

        $this->types[$name] = $typeDefinition;
    }

    /**
     * @param $name
     * @return Type
     */
    public function resolve($name)
    {
        if (array_key_exists($name, $this->types)) {
            return $this->types[$name];
        }

        throw new \RuntimeException(sprintf(
            'Unable to resolve type "%s"', $name
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
