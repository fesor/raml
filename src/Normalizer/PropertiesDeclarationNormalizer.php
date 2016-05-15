<?php

namespace Fesor\RAML\Normalizer;

class PropertiesDeclarationNormalizer
{
    private $typeNormalizer;

    public function __construct(Normalizer $typeNormalizer)
    {
        $this->typeNormalizer = $typeNormalizer;
    }

    public function normalize($value)
    {
        return $this->typeNormalizer->normalize([
            'properties' => $value
        ])['properties'];
    }
}
