<?php

namespace Fesor\RAML\Normalizer;

use function Fesor\RAML\excludingKeys;
use function Fesor\RAML\onlyWithinKeys;

class ResourcesNormalizer implements Normalizer
{
    public function normalize($value)
    {
        $methods = $this->collectMethods($value);
        $value['methods'] = array_values($methods);
        $value = array_diff_key($value, $methods);

        return $value;
    }

    private function collectMethods($value)
    {
        $methods = onlyWithinKeys($value, [
            'get', 'patch', 'put', 'post', 'delete', 'options', 'head'
        ]);

        foreach ($methods as $httpMethod => &$methodDefinition)
        {
            $methodDefinition['method'] = $httpMethod;
        }

        return $methods;
    }
}
