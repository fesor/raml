<?php

namespace Fesor\RAML\Type;

class ObjectType extends Type
{
    public function __construct($name, array $data, $parentType = null)
    {
        parent::__construct($name, $data, $parentType);
    }
}
