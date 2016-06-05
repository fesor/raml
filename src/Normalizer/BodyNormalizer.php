<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Body;
use function Fesor\RAML\isValidMediaType;

class BodyNormalizer extends AbstractNormalizer
{
    public function normalize($value, array $path)
    {

        if ([] === $path) {
            $this->builder->setDefaultMediaType(
                isset($value['mediaType']) ?
                    $value['mediaType'] : 'application/json'
            );

            return $value;
        }

        if (end($path) !== 'body') {
            return $value;
        }

        return $this->processBodyDeclaration($value);
    }

    private function processBodyDeclaration($body)
    {
        $bodyDeclarations = [];
        if (is_string($body)) {
            $bodyDeclarations[] = [
                'body' => $body,
                'mediaType' => $this->builder->getDefaultMediaType()
            ];
        } else {
            $bodyDeclarations = $this->handleContentTypeMap($body);
        }

        return array_map(function ($bodyDeclaration) {

            return new Body(
                $bodyDeclaration['mediaType'],
                $this->builder->createType($bodyDeclaration['body'])
            );
        }, $bodyDeclarations);
    }

    private function handleContentTypeMap(array $body)
    {
        $mediaTypes = array_filter(array_keys($body), function ($key) {
            return isValidMediaType($key);
        });

        if (empty($mediaTypes)) {

            return $this->handleContentTypeMap([
                $this->builder->getDefaultMediaType() => $body
            ]);
        }

        $bodies = [];
        foreach ($mediaTypes as $mediaType) {

            $bodies[] = [
                'body' => $body[$mediaType],
                'mediaType' => $mediaType
            ];
        }

        return $bodies;
    }
}
