<?php

namespace Fesor\RAML\Type;

class TypeRegistry implements TypeResolver
{
    private $types = [];

    public function register(Type $typeDefinition)
    {
        if (null === $typeDefinition->typeName()) {
            throw new \InvalidArgumentException('Unable to register unnamed type');
        }

        $this->types[$typeDefinition->typeName()] = $typeDefinition;
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
}
