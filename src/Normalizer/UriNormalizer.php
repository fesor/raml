<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Uri;

class UriNormalizer extends AbstractNormalizer
{
    use TypeConstructorAware;
    
    public function normalize(array $value)
    {
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

    private function processUriParameters(array $resource)
    {
        $uriParams = isset($resource['uriParameters']) ?
            $resource['uriParameters'] : [];

        return $this->constructType([
            'type' => 'object',
            'properties' => $uriParams
        ]);
    }
}
