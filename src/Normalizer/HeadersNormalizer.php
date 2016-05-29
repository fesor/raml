<?php

namespace Fesor\RAML\Normalizer;

class HeadersNormalizer extends AbstractNormalizer
{
    public function normalize($value, array $path)
    {
        if (end($path) !== 'headers') {
            return $value;
        }

        return $this->builder->createType([
            'properties' => $value
        ]);
    }
}
