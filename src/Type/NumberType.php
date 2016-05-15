<?php

namespace Fesor\RAML\Type;

use function Fesor\RAML\withDefaultValues;

class NumberType extends ScalarType
{
    private static $supportedFormats = ['int64', 'int64', 'int', 'long', 'float', 'double', 'int16', 'int8'];

    private $data;

    public function __construct($name, array $data, $parentType = null)
    {
        $this->data = withDefaultValues([
            'minimum' => null,
            'maximum' => null,
            'format' => null,
            'multipleOf' => null
        ], $data);

        if (
            null !== $this->data['format']
            && !in_array($this->data['format'], self::$supportedFormats)
        ) {
            throw new \RuntimeException(sprintf(
                'Unsupported format "%s" for numeric value',
                $this->data['format']
            ));
        }

        parent::__construct($name, $data, $parentType);
    }


    public function getMinimum()
    {
        return $this->data['minimum'];
    }

    public function getMaxium()
    {
        return $this->data['maximum'];
    }

    public function getFormat()
    {
        return $this->data['format'];
    }

    public function getMultipleOf()
    {
        return $this->data['multipleOf'];
    }
}
