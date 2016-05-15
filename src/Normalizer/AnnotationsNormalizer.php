<?php

namespace Fesor\RAML\Normalizer;

class AnnotationsNormalizer implements Normalizer
{
    private $next;

    /**
     * AnnotationsNormalizer constructor.
     * @param Normalizer $next
     */
    public function __construct(Normalizer $next)
    {
        $this->next = $next;
    }

    public function normalize($value)
    {
        $annotations = $this->collectAnnotations(array_keys($value));
        foreach ($annotations as $key => $annotation) {
            $value['annotations'][$annotation] = $value[$key];
        }

        return $this->next->normalize(array_diff_key($value, $annotations));
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
