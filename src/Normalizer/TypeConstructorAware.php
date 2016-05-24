<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Type\TypeConstructor;

interface TypeConstructorAware
{
    public function setTypeConstructor(TypeConstructor $typeConstructor);
}