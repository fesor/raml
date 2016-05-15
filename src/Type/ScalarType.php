<?php

namespace Fesor\RAML\Type;

use function Fesor\RAML\withDefaultValues;

class ScalarType extends Type
{
    private $data;

    public function __construct($name, array $data, $parentType = null)
    {
        $this->data = withDefaultValues([
            'enum' => []
        ], $data);

        parent::__construct($name, $data, $parentType);
    }

    public function getEnum()
    {
        return $this->data['enum'];
    }
}
