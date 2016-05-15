<?php

namespace Fesor\RAML\Normalizer;

use function Fesor\RAML\onlyWithinKeys;
use function Fesor\RAML\excludingKeys;

class RootNormalizer implements Normalizer
{
    private $normalizers;

    /**
     * RootNormalizer constructor.
     * @param NormalizerRegistry $normalizers
     */
    public function __construct(NormalizerRegistry $normalizers)
    {
        $this->normalizers = $normalizers;
    }

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

        if (isset($value['annotationTypes'])) {
            $value['annotationTypes'] = $this->normalizeAnnotationTypes($value['annotationTypes']);
        }

        return excludingKeys($value, $resourcesKeys);
    }

    private function collectResourceKeys($value)
    {
        return array_filter(array_keys($value), function ($key) {
            return 0 === mb_strpos($key, '/');
        });
    }

    private function normalizeAnnotationTypes(array $annotationTypes)
    {
        return array_map(function ($type) {
            return $this->normalizers->getNormalizer('type')->normalize($type);
        }, $annotationTypes);
    }
}
