<?php

namespace Fesor\RAML;

use Fesor\RAML\Type\Type;

class Body
{
    private $mediaType;

    private $type;

    /**
     * Body constructor.
     * @param string $mediaType
     * @param Type $type
     */
    public function __construct($mediaType, Type $type)
    {
        $this->mediaType = $mediaType;
        $this->type = $type;
    }
}