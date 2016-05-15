<?php

namespace Fesor\RAML\Normalizer;

class NormalizerRegistry
{
    private $normalizersMap;

    public function addNormalizer($name, $normalizer)
    {
        $this->normalizersMap[$name] = $normalizer;
    }

    public function getNormalizer($name)
    {
        if (!isset($this->normalizersMap[$name])) {
            throw new \RuntimeException(sprintf('Normalizer "%s" is nor registered', $name));
        }

        return $this->normalizersMap[$name];
    }
}
