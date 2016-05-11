<?php

namespace Fesor\RAML\Normalizer;

use Fesor\RAML\RAMLLoader;

class ImportNormalizer implements Normalizer
{
    const IMPORT_TAG = '!include';

    /**
     * @var RAMLLoader
     */
    private $loader;

    /**
     * ImportNormalizer constructor.
     * @param RAMLLoader $loader
     */
    public function __construct(RAMLLoader $loader)
    {
        $this->loader = $loader;
    }

    public function normalize($value)
    {
        $value = $this->mapImports(
            $value,
            $this->importData($this->collectImports($value))
        );

        return $value;
    }

    private function collectImports($value)
    {
        $imports = [];
        foreach ($value as $key => $node) {
            if (!is_string($node) || 0 !== mb_strpos($node, self::IMPORT_TAG)) {
                continue;
            }

            $imports[$key] = mb_substr($node, strlen(self::IMPORT_TAG)+1);
        }

        return $imports;
    }

    private function importData(array $imports)
    {
        return array_map(function ($path) {
            return $this->loader->load($path);
        }, $imports);
    }

    private function mapImports($value, $imports)
    {
        foreach($imports as $key => $importedData) {
            $value[$key] = $importedData;
        }

        return $value;
    }
}
