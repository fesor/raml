<?php

namespace Fesor\RAML\Normalizer;

class RamlNormalizer implements Normalizer
{
    public function supports(array $path)
    {
        return $path === [];
    }

    public function supportsDirection()
    {
        return self::DIRECTION_OUTWARD;
    }

    public function priority()
    {
        return 1000;
    }

    public function normalize(array $value)
    {
        
    }
}
