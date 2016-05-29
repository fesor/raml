<?php

namespace Fesor\RAML\Type;

/**
 * Class BathcTypeResolver
 * @package Fesor\RAML
 */
class TypesMapFactory implements TypeResolver
{
    private $typeConstructor;

    private $typeResolutionQueue;

    private $typeDeclarationStack;

    private $types;

    public function __construct()
    {
        $this->typeConstructor = new TypeConstructor($this);
        $this->typeResolutionQueue = [];
        $this->typeDeclarationStack = [];
        $this->types = [];
    }

    /**
     * @param array $typeMap
     * @return Type[] hash map
     */
    public function create($typeMap)
    {
        $this->typeResolutionQueue = $typeMap;

        foreach ($typeMap as $typeName => $typeDeclaration) {
            if ($this->isTypeAlreadyResolved($typeName)) {
                continue;
            }

            $this->resolve($typeName);
        }

        return $this->types;
    }

    public function resolve($typeName)
    {
        if ($this->isTypeAlreadyResolved($typeName)) {
            if (!isset($this->types[$typeName])) {
                throw new \RuntimeException(sprintf(
                    'Unable to resolve type "%s"', $typeName
                ));
            }

            return $this->types[$typeName];
        }

        if (in_array($typeName, $this->typeDeclarationStack)) {
            throw new \RuntimeException('Cyclic dependencies');
        }
        array_push($this->typeDeclarationStack, $typeName);
        $typeDeclaration = $this->typeResolutionQueue[$typeName];
        $type = $this->typeConstructor->createType($typeDeclaration, $typeName);
        unset($this->typeResolutionQueue[$typeName]);
        array_pop($this->typeDeclarationStack);

        $this->register($type);

        return $type;
    }

    public function register(Type $type)
    {
        if (null === $type->typeName()) {
            throw new \InvalidArgumentException('Unable to register unnamed type');
        }
        $this->types[$type->typeName()] = $type;
    }

    private function isTypeAlreadyResolved($typeName)
    {
        return !isset($this->typeResolutionQueue[$typeName]);
    }
}