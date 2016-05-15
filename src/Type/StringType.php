<?php

namespace Fesor\RAML\Type;

use function Fesor\RAML\withDefaultValues;

class StringType extends ScalarType
{
    private $data;

    public function __construct($name, array $data, Type $parentType = null)
    {
        $this->data = withDefaultValues([
            'pattern' => null,
            'minLength' => 0,
            'maxLength' => 2147483647
        ], $data);

        parent::__construct($name, $data, $parentType);
    }

    public function getPattern()
    {
        return $this->data['pattern'];
    }

    public function getMinLength()
    {
        return $this->data['minLength'];
    }

    public function getMaxLength()
    {
        return $this->data['maxLength'];
    }
}
