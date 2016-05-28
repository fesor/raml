<?php

namespace Fesor\RAML\Normalizer;

class ResponseCollectionNormalizer extends AbstractNormalizer
{
    public function normalize(array $value)
    {
        $responses = [];
        foreach ($value as $statusCode => $response) {
            $response['statusCode'] = $statusCode;
            $responses[] = $response;
        }

        return $responses;
    }

    public function supports(array $path)
    {
        return count($path) >= 2 && end($path) === 'responses';
    }
}
