<?php

namespace Fesor\RAML\Type;

class NullType extends Type
{
    /**
     * NullType constructor.
     */
    public function __construct(array $facets = [])
    {
        parent::__construct($facets);
    }
}
