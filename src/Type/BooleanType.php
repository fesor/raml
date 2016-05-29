<?php

namespace Fesor\RAML\Type;

class BooleanType extends Type
{
    protected function knownFacets()
    {
        return $this->extendKnownFacets();
    }
}
