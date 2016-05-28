<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Body;
use Fesor\RAML\Type\TypeConstructor;
use function Fesor\RAML\isValidMediaType;

class BodyNormalizer extends AbstractNormalizer
{
    use TypeConstructorAware;

    private $defaultMediaType;

    private $defaultMediaTypeChecked = false;

    public function supports(array $path)
    {
        return [] === $path || (count($path) >= 2 && in_array($path[count($path) - 2], [
            'responses', 'methods'
        ], true));
    }

    public function normalize(array $value)
    {
        if (!$this->defaultMediaTypeChecked) {
            $this->defaultMediaTypeChecked = true;
            $this->defaultMediaType = isset($value['mediaType']) ?
                $value['mediaType'] : 'application/json';

            return $value;
        }

        if (!array_key_exists('body', $value)) {
            return $value;
        }

        $value['body'] = $this->processBodyDeclaration($value['body']);

        return $value;
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
