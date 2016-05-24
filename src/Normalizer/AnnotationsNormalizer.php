<?php

namespace Fesor\RAML\Normalizer;

class AnnotationsNormalizer implements Normalizer
{
    public function supports(array $path)
    {
        return !in_array('annotations', $path);
    }

    public function supportsDirection()
    {
        return self::DIRECTION_INWARD;
    }

    public function priority()
    {
        return 50;
    }

    public function normalize(array $value)
    {
        $annotations = $this->collectAnnotations(array_keys($value));
        foreach ($annotations as $key => $annotation) {
            $value['annotations'][$annotation] = $value[$key];
        }

        return array_diff_key($value, $annotations);
    }

    private function collectAnnotations($keys)
    {
        return array_filter(array_map(function ($key) {
            if (!preg_match('/^\((.+)\)$/U', $key, $matches)) {
                return null;
            }

            return $matches[1];
        }, array_combine($keys, $keys)));
    }
}
