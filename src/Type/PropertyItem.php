<?php

namespace Fesor\RAML\Type;

class PropertyItem
{
    private $required;

    private $type;

    /**
     * PropertyItem constructor.
     * @param Type|null $type
     * @param bool $required
     */
    public function __construct(Type $type = null, $required = true)
    {
        $this->type = $type;
        $this->required = $required;
    }

    public function type()
    {
        return $this->type;
    }

    public function required()
    {
        return $this->required;
    }

}