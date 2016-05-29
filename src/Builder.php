<?php

namespace Fesor\RAML;

use Fesor\RAML\Type\Type;
use Fesor\RAML\Type\TypeConstructor;
use Fesor\RAML\Type\TypeResolver;

class Builder
{
    private $typeResolver;

    private $typeConstructor;

    private $annotations;

    private $traits;

    private $resourceTypes;

    /**
     * RamlBuilder constructor.
     * @param TypeConstructor $typeConstructor
     * @param TypeResolver $typeResolver
     */
    public function __construct(TypeConstructor $typeConstructor, TypeResolver $typeResolver)
    {
        $this->typeConstructor = $typeConstructor;
        $this->typeResolver = $typeResolver;
        $this->annotations = [];
        $this->traits = [];
        $this->resourceTypes = [];
    }

    /**
     * @param TypeResolver $typeResolver
     */
    public function setTypeResolver(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
        $this->typeConstructor = new TypeConstructor($typeResolver);
    }

    /**
     * @param string $typeName
     * @return Type
     */
    public function createType($typeName)
    {
        return $this->typeConstructor->createType($typeName);
    }

    /**
     * @param Type $type
     */
    public function registerType(Type $type)
    {
        $this->typeResolver->register($type);
    }
}