<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\RAMLLoader;

class ModulizationNormalizer extends AbstractNormalizer
{
    private $loader;

    public function __construct(RAMLLoader $loader)
    {
        $this->loader = $loader;
    }

    public function normalize($value, array $path)
    {
        if ([] !== $path) {
            return $value;
        }

        return $this->handleImports($value);
    }

    private function handleImports($value)
    {
        $imports = [];
        $this->collectImports($value, [], $imports);

        foreach ($imports as $import) {
            $import['node'] = $this->loader->load($import['file']);
        }

        
        
        return $value;
    }

    private function collectImports(&$value, array $path, array &$imports = [])
    {
        if (is_string($value) && mb_strpos($value, '!include') === 0) {
            $imports[] = [
                'node' => &$value,
                'path' => $path,
                'file' => trim(mb_substr($value, 8))
            ];
        }

        if (!is_array($value)) {
            return;
        }

        foreach ($value as $key => &$node) {
            $this->collectImports($node, array_merge($path, [$key]), $imports);
        }
    }
}
