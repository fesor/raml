<?php

namespace Fesor\RAML;

use Fesor\RAML\Type\ObjectType;

class Response
{
    private $statusCode;
    
    private $description;
    
    private $headers;

    private $body;

    /**
     * Response constructor.
     * @param int $statusCode
     * @param Body $body
     * @param ObjectType $headers
     */
    public function __construct($statusCode, Body $body, ObjectType $headers)
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }
}
