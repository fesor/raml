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

    public function setTypeConstructor(TypeConstructor $typeConstructor)
    {
        $this->typeConstuctor = $typeConstructor;
    }

    protected function constructType($typeDeclaration, $name = null)
    {
        return $this->typeConstuctor->createType($typeDeclaration, $name);
    }
}