<?php

namespace Fesor\RAML\Type;

/**
 * Class BathcTypeResolver
 * @package Fesor\RAML
 * @internal
 */
class TypeRegistryFactory implements TypeResolver
{
    private $typeConstructor;

    private $typeResolutionQueue;

    private $typeDeclarationStack;

    private $typesRegistry;

    public function __construct()
    {
        $this->typesRegistry = new TypeRegistry();
        $this->typeConstructor = new TypeConstructor($this);
        $this->typeResolutionQueue = [];
        $this->typeDeclarationStack = [];
    }

    /**
     * @param array $typeMap
     * @return TypeRegistry
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

        return $this->typesRegistry;
    }

    public function resolve($typeName)
    {
        if ($this->isTypeAlreadyResolved($typeName)) {
            return $this->typesRegistry->resolve($typeName);
        }

        if (in_array($typeName, $this->typeDeclarationStack)) {
            throw new \RuntimeException('Cyclic dependencies');
        }
        array_push($this->typeDeclarationStack, $typeName);
        $typeDeclaration = $this->typeResolutionQueue[$typeName];
        $type = $this->typeConstructor->createType($typeDeclaration, $typeName);
        unset($this->typeResolutionQueue[$typeName]);
        array_pop($this->typeDeclarationStack);

        $this->typesRegistry->register($type);

        return $type;
    }

    private function isTypeAlreadyResolved($typeName)
    {
        return !isset($this->typeResolutionQueue[$typeName]);
    }
}