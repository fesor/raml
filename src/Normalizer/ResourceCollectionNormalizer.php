<?php

namespace Fesor\RAML\Normalizer;

use function Fesor\RAML\onlyWithinKeys;
use function Fesor\RAML\excludingKeys;

class ResourceCollectionNormalizer implements Normalizer
{
    public function supportsDirection()
    {
        return self::DIRECTION_INWARD;
    }

    public function priority()
    {
        return 10;
    }

    public function supports(array $path)
    {
        return empty($path) || ('resourceTypes' === $path[0] && 2 === count($path));
    }

    public function normalize($value, array $path)
    {
        if (!$this->supports($path)) {
            return $value;
        }
        
         $resourceKeys = $this->collectResourceKeys($value);
        $resources = onlyWithinKeys($value, $resourceKeys);

        foreach ($resources as $uri => $resource) {
            $collected = $this->normalize($resource, array_merge($path, $path));
            $collected['uri'] = $uri;
            $resources[$uri] = $collected;
        }

        $result = excludingKeys($value, $resourceKeys);
        $result['resources'] = array_values($resources);

        return $result;
    }

    private function collectResourceKeys(array $value)
    {
        return array_filter(array_keys($value), function ($key) {
            return 0 === mb_strpos($key, '/');
        });
    }
}
