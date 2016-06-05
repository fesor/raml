<?php

namespace Fesor\RAML;

use Fesor\RAML\Normalizer\Normalizer;
use Symfony\Component\Yaml\Parser as YamlParser;

class RamlParser
{
    private $yamlParser;

    private $normalizer;

    /**
     * RamlParser constructor.
     * @param YamlParser $yamlParser
     * @param Normalizer $normalizer
     */
    public function __construct(YamlParser $yamlParser, Normalizer $normalizer)
    {
        $this->yamlParser = $yamlParser;
        $this->normalizer = $normalizer;
    }

    public static function parseFile($filePath)
    {
        $parser = (new RamlParserFactory())->getRamlParser();
        
        return $parser->parse(file_get_contents($filePath));
    }

    public function parse($raml, $asFragment = null)
    {
        $metadata = $this->retreaveMetadata($raml);

        $this->validateMetadata($metadata, $asFragment);

        $yaml = $this->yamlParser->parse($raml);
        $yaml['_metadata'] = $metadata;

        return $this->normalizer->normalize($yaml, []);
    }

    private function retreaveMetadata($raml)
    {
        if (preg_match('/^#%RAML[\t ]+(\d+\.\d+)([\t ]+(\w+))?\s*/', $raml, $matches)) {
            return [
                'version' => (float) $matches[1],
                'fragment' => isset($matches[3]) ? $matches[3] : null
            ];
        }

        return [];
    }

    private function validateMetadata($metadata, $expectedFragmentType)
    {
        if (empty($metadata)) {
            return;
        }

        if ($metadata['version'] !== 1.0) {
            throw new \RuntimeException(sprintf(
                'This parser doesn\'t support this version or RAML specification',
                $metadata['version']
            ));
        }

        if (!isset($metadata['fragment'])) {
            return;
        }

        if (!in_array($metadata['fragment'], [
            'DocumentationItem', 'DataType', 'NamedExample', 'ResourceType', 'Trait',
            'AnnotationTypeDeclaration', 'Library', 'Overlay', 'Extension', 'SecurityScheme'
        ])) {
            throw new \RuntimeException(sprintf(
                'Unknown RAML fragment "%s"',
                $metadata['fragment']
            ));
        }

        if ($expectedFragmentType && $expectedFragmentType !== $metadata['fragment']) {
            throw new \RuntimeException(sprintf(
                'Expected to get "%s" RAML fragment, "%s" given',
                $expectedFragmentType,
                $metadata['fragment']
            ));
        }
    }
}
