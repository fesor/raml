<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Type\TypeConstructor;
use Fesor\RAML\Type\TypeResolver;

trait TypeConstructorAware
{
    /**
     * @var TypeConstructor
     */
    private $typeConstuctor;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    public function setTypeConstructor(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $this->typeConstuctor = $typeConstructor;
        $this->typeResolver = $resolver;
    }

    protected function constructType($typeDeclaration)
    {
        return $this->typeConstuctor->construct($typeDeclaration, $this->typeResolver);
    }
}