<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Response;
use Fesor\RAML\Type\ObjectType;

class ResponseNormalizer extends AbstractNormalizer
{
    public function supportsDirection()
    {
        return self::DIRECTION_OUTWARD;
    }

    public function normalize($value, array $path)
    {
        if(end($path) !== 'responses' || !is_array($value)) {
            return $value;
        }

        return array_map(function ($responseDeclaration) {
            
            return new Response(
                $responseDeclaration['statusCode'],
                (isset($responseDeclaration['description']) ? $responseDeclaration['description'] : ''),
                $responseDeclaration['body'],
                isset($responseDeclaration['headers']) ? $responseDeclaration['headers'] : new ObjectType([])
            );
        }, $value);
    }
}
