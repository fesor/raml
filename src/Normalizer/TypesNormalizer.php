<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Type\TypesMapFactory;

class TypesNormalizer extends AbstractNormalizer
{
    private $typeRegistryFactory;

    public function __construct(TypesMapFactory $typeRegistryFactory)
    {
        $this->typeRegistryFactory = $typeRegistryFactory;
    }

    public function priority()
    {
        return 20;
    }

    public function normalize($value, array $path)
    {
        if ([] !== $path) {
            return $value;
        }

        $types = [];
        if (isset($value['types'])) {
            $types = $value['types'];
        }
        unset($value['types']);

        $types = $this->typeRegistryFactory->create($types);
        foreach ($types as $type) {
            $this->builder->registerType($type);
        }

        return $value;
    }
}
