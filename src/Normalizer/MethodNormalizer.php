<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Method;

class MethodNormalizer extends AbstractNormalizer
{
    public function supportsDirection()
    {
        return self::DIRECTION_OUTWARD;
    }

    public function normalize($value, array $path)
    {
        if (count($path) < 2 || 'methods' !== $path[count($path) - 2]) {
            return $value;
        }

        return Method::fromArray($value);
    }
}
