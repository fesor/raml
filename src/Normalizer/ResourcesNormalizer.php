<?php

namespace Fesor\RAML\Normalizer;

class ResourcesNormalizer implements Normalizer
{
    public function normalize($value)
    {
        $resourcesKeys = $this->collectResourceKeys($value);
        $resources = array_intersect_key($value, array_flip($resourcesKeys));
        foreach ($resources as $uri => &$resource) {
            $resource['uri'] = $uri;
        }
        $value['resources'] = array_map(function ($resource) {
            return $this->normalize($resource);
        }, array_values($resources));

        return array_diff_key($value, array_flip($resourcesKeys));
    }

    private function collectResourceKeys($value)
    {
        return array_filter(array_keys($value), function ($key) {
            return 0 === mb_strpos($key, '/');
        });
    }
}
