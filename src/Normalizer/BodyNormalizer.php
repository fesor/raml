<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Body;
use function Fesor\RAML\isValidMediaType;

class BodyNormalizer extends AbstractNormalizer
{
    use TypeConstructorAware;

    private $defaultMediaType;

    public function normalize($value, array $path)
    {

        if ([] === $path) {
            $this->defaultMediaType = isset($value['mediaType']) ?
                $value['mediaType'] : 'application/json';

            return $value;
        }

        if (end($path) !== 'body') {
            return $value;
        }

        return $this->processBodyDeclaration($value);
    }

    private function processBodyDeclaration($body)
    {
        if (is_string($body)) {
            // todo: add error in case if no default media type specified
            $defaultMediaType = $this->defaultMediaType ?: 'application/json';

            return new Body($defaultMediaType, $this->constructType($body));
        }

        $body = $this->handleContentTypeMap($body);

        return array_map(function ($bodyDeclaration) {

            return new Body(
                $bodyDeclaration['mediaType'],
                $this->constructType($bodyDeclaration['type'])
            );
        }, $body);
    }

    private function handleContentTypeMap(array $body)
    {
        $mediaTypes = array_filter(array_keys($body), function ($key) {
            return isValidMediaType($key);
        });

        if (empty($mediaTypes)) {
            $body['mediaType'] = $this->defaultMediaType;

            return [$body];
        }

        $bodies = [];
        foreach ($mediaTypes as $mediaType) {
            $bodyDeclaration = $body[$mediaType];

            if (is_string($bodyDeclaration)) {
                $bodyDeclaration = [
                    'type' => $bodyDeclaration
                ];
            }

            $bodyDeclaration['mediaType'] = $mediaType;
            $bodies[] = $bodyDeclaration;
        }

        return $bodies;
    }
}
