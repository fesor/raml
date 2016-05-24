<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Type\TypeConstructor;

class BodyNormalizer extends AbstractNormalizer implements TypeConstructorAware
{
    private $typeConstructor;

    public function setTypeConstructor(TypeConstructor $typeConstructor)
    {
        $this->typeConstructor = $typeConstructor;
    }

    public function supports(array $path)
    {
        return end($path) === 'body';
    }

    public function normalize(array $value)
    {
        
    }
}
