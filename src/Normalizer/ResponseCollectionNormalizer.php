<?php

namespace Fesor\RAML\Normalizer;

class ResponseCollectionNormalizer extends AbstractNormalizer
{
    public function normalize($value, array $path)
    {
        if (!$this->supports($path)) {
            return $value;
        }
        
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
