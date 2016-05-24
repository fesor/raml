<?php

namespace Fesor\RAML\Normalizer;

use function Fesor\RAML\excludingKeys;
use function Fesor\RAML\onlyWithinKeys;

class MethodCollectionNormalizer implements Normalizer
{
    public function priority()
    {
        return 50;
    }

    public function supportsDirection()
    {
        return self::DIRECTION_INWARD;
    }

    public function supports(array $path)
    {
        return count($path) >= 2 && $path[count($path) - 2] === 'resources';
    }

    public function normalize(array $value)
    {
        $verbs = ['get', 'patch', 'put', 'post', 'delete', 'options', 'head'];
        $result = excludingKeys($value, $verbs);
        $methods = onlyWithinKeys($value, $verbs);

        foreach ($methods as $verb => $methodDeclaration) {
            $methodDeclaration['method'] = $verb;
            $methods[$verb] = $methodDeclaration;
        }

        $result['methods'] = array_values($methods);

        return $result;
    }
}
