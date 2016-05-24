<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\AnnotationRef;
use function \Fesor\RAML\onlyWithinKeys;
use function \Fesor\RAML\excludingKeys;

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
        $keys = array_keys($value);
        $annotationsMap = array_filter(array_map(function ($key) {
            if (!preg_match('/^\((.+)\)$/U', $key, $matches)) {
                return null;
            }

            return $matches[1];
        }, array_combine($keys, $keys)));

        $annotations = array_combine(
            array_values($annotationsMap),
            array_intersect_key($value, $annotationsMap)
        );
        $value = array_diff_key($value, $annotationsMap);
        $value['annotations'] = $this->processAnnotations($annotations);

        return $value;
    }
    
    private function processAnnotations(array $annotationValues)
    {
        $annotations = [];
        foreach ($annotationValues as $annotationName => $value) {
            $annotations[] = new AnnotationRef($annotationName, $value);
        }

        return $annotations;
    }
}
