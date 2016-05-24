<?php

namespace Fesor\RAML;

class AnnotationRef
{
    private $name;

    private $value;

    /**
     * AnnotationRef constructor.
     * @param string $name
     * @param mixed $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}