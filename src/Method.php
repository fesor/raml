<?php


namespace Fesor\RAML;


use Fesor\RAML\Type\ObjectType;

class Method
{
    private $httpVerb;

    private $displayName;

    private $description;

    private $queryParameters;

    private $protocols;

    private $headers;

    private $queryString;

    private $requestBody;

    private $responses;

    private $securedBy;

    private function __construct() {}

    public static function fromArray(array $raml)
    {
        $defaults = [
            'displayName' => '',
            'description' => '',
            'headers' => new ObjectType([]),
            'queryString' => null,
            'requestBody' => null,
            'responses' => null,
            'securedBy' => null,
        ];

        $map = array_merge(
            [
                'method' => 'httpVerb',
            ],
            array_combine(
                array_keys($defaults),
                array_keys($defaults)
            )
        );

        $data = array_replace($defaults, $raml);
        $instance = new static();
        foreach ($map as $from => $to) {
            $instance->$to = $data[$from];
        }

        return $instance;
    }
}