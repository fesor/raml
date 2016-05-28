<?php

namespace Fesor\RAML\Normalizer;

class RecursiveNormalizer implements Normalizer
{
    private $inwardNormalizers;

    private $outwardNormalizers;

    public function __construct(array $normalizers)
    {
        $this->inwardNormalizers = $this->sortNormalizers($normalizers, Normalizer::DIRECTION_INWARD);
        $this->outwardNormalizers = $this->sortNormalizers($normalizers, Normalizer::DIRECTION_OUTWARD);
    }

    public function priority()
    {
        return 0;
    }

    public function supportsDirection()
    {
        return self::DIRECTION_ANY;
    }

    public function normalize($value, array $path = [])
    {
        return $this->normalizeNode($value, $path);
    }

    /**
     * @param array $value
     * @param string[] $path
     * @return array
     */
    private function normalizeNode(array $value, array $path)
    {
        $value = $this->normalizeValue($value, $path, $this->inwardNormalizers);
        foreach ($value as $key => &$nodeValue) {
            if (is_array($nodeValue)) {
                $nodeValue = $this->normalizeNode($nodeValue, array_merge($path, [$key]));
            }
        }

        return $this->normalizeValue($value, $path, $this->outwardNormalizers);
    }

    /**
     * @param array $value
     * @param string[] $path
     * @param Normalizer[] $normalizers
     * @return array
     */
    private function normalizeValue(array $value, array $path, array $normalizers)
    {
        foreach ($normalizers as $normalizer) {
            $value = $normalizer->normalize($value, $path);
        }

        return $value;
    }

    /**
     * @param Normalizer[] $normalizers
     * @param int $direction
     * @return Normalizer[]
     */
    private function sortNormalizers(array $normalizers, $direction)
    {
        $normalizers = array_filter($normalizers, function (Normalizer $normalizer) use ($direction) {
            return in_array($normalizer->supportsDirection(), [
                Normalizer::DIRECTION_ANY,
                $direction
            ]);
        });

        usort($normalizers, function (Normalizer $a, Normalizer $b) {
            return $a->priority() - $b->priority();
        });

        return $normalizers;
    }
}
