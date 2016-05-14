<?php

namespace Fesor\RAML\Normalizer;

use function Fesor\RAML\excludingKeys;
use function Fesor\RAML\onlyWithinKeys;

class ResourcesNormalizer implements Normalizer
{
    public function normalize($value)
    {
        $resourcesKeys = $this->collectResourceKeys($value);
        $resources = onlyWithinKeys($value, $resourcesKeys);
        foreach ($resources as $uri => &$resource) {
            $resource['uri'] = $uri;
        }
        $value['resources'] = array_map(function ($resource) {
            return $this->normalize($resource);
        }, array_values($resources));

        return excludingKeys($value, $resourcesKeys);
    }

    private function collectResourceKeys($value)
    {
        return array_filter(array_keys($value), function ($key) {
            return 0 === mb_strpos($key, '/');
        });
    }
}
