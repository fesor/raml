<?php

namespace Fesor\RAML\Normalizer;

class MethodNormalizer implements Normalizer
{
    public function normalize($value)
    {
        $methods = $this->collectMethods($value);
        $value['methods'] = array_values($methods);

        return array_diff_key($value, $methods);
    }

    private function collectMethods($value)
    {
        $methods = array_intersect_key($value, array_flip([
            'get', 'patch', 'put', 'post', 'delete', 'options', 'head'
        ]));

        foreach ($methods as $httpMethod => &$methodDefinition)
        {
            $methodDefinition['method'] = $httpMethod;
        }

        return $methods;
    }
}
