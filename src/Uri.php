<?php

namespace Fesor\RAML;

use Fesor\RAML\Type\ObjectType;

class Uri
{
    private $uri;

    private $uriParameters;

    private $parent;

    public function __construct($uri, ObjectType $uriParameters, Uri $parent = null)
    {
        $this->uri = $uri;
        $this->uriParameters = $uriParameters;
        $this->parent = $parent;
    }

    public function uriTemplate()
    {
        return $this->uri;
    }

    public function absoluteUri()
    {
        $base = '';
        if ($this->parent) {
            $base = $this->parent->absoluteUri();
        }

        return $base . $this->uri;
    }

    public function parameters()
    {
        return $this->uriParameters;
    }
}
