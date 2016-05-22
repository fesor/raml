<?php

namespace Fesor\RAML\Type;

/**
 * Class BathcTypeResolver
 * @package Fesor\RAML
 */
class BatchTypeResolver implements TypeResolver
{

    private $typeConstructor;

    private $typeResolutionQueue;

    private $typeDeclarationStack;

    private $typesRegistry;

    public function __construct(TypeConstructor $typeConstructor, TypeRegistry $typeRegistry)
    {
        $this->typeConstructor = $typeConstructor;
        $this->typeResolutionQueue = [];
        $this->typeDeclarationStack = [];
        $this->typesRegistry = $typeRegistry;
    }

    public function resolveTypeMap($typeMap)
    {
        $this->typeResolutionQueue = $typeMap;

        foreach ($typeMap as $typeName => $typeDeclaration) {
            if ($this->isTypeAlreadyResolved($typeName)) {
                continue;
            }

            $this->resolve($typeName);
        }
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
        $type = $this->typeConstructor->construct($typeDeclaration, $this);
        unset($this->typeResolutionQueue[$typeName]);
        array_pop($this->typeDeclarationStack);

        $this->typesRegistry->register($typeName, $type);

        return $type;
    }

    private function isTypeAlreadyResolved($typeName)
    {
        return !isset($this->typeResolutionQueue[$typeName]);
    }
}