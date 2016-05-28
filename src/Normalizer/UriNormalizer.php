<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Uri;

class UriNormalizer extends AbstractNormalizer
{
    use TypeConstructorAware;
    
    public function normalize($value, array $path)
    {
        if (!$this->supports($path)) {
            return $value;
        }
        
        if (!array_key_exists('uri', $value)) {
            $value['baseUri'] = new Uri(
                isset($value['baseUri']) ? $value['baseUri'] : '',
                $this->processUriParameters(
                    $value,
                    'baseUriParameters',
                    ['version' => 'string']
                )
            );

            unset($value['baseUriParameters']);

            return $value;
        }

        return $this->processUri($value);
    }

    public function supports(array $path)
    {
        return [] === $path
            || (count($path) === 2 && $path[0] === 'resources' && is_numeric($path[1]));
    }

    private function processUri(array $resource, Uri $parent = null)
    {
        $uri = new Uri(
            $resource['uri'],
            $this->processUriParameters($resource),
            $parent
        );

        foreach ($resource['resources'] as &$childResource) {
            $childResource = $this->processUri($childResource, $uri);
        }

        $resource['uri'] = $uri;
        unset($resource['uriParameters']);

        return $resource;
    }

    private function processUriParameters(array $resource, $key = 'uriParameters', array $additionalProperties = [])
    {
        $uriParams = isset($resource[$key]) ?
            $resource[$key] : [];

        $uriParams = array_replace($additionalProperties, $uriParams);

        return $this->constructType([
            'type' => 'object',
            'properties' => $uriParams
        ]);
    }
}
