<?php

namespace Fesor\RAML;

use Fesor\RAML\Type\ObjectType;

class Response
{
    private $statusCode;
    
    private $description;
    
    private $headers;

    private $bodies;

    /**
     * Response constructor.
     * @param int $statusCode
     * @param string $description
     * @param Body[] $bodies
     * @param ObjectType $headers
     */
    public function __construct($statusCode, $description, array $bodies, ObjectType $headers)
    {
        $this->bodies = $bodies;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }
}
