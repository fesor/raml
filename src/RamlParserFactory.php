<?php

namespace Fesor\RAML;

use Fesor\RAML\Normalizer;
use Fesor\RAML\Type\TypeConstructor;
use Fesor\RAML\Type\TypeRegistry;
use Fesor\RAML\Type\TypesMapFactory;
use Symfony\Component\Yaml\Parser as YamlParser;

class RamlParserFactory
{
    public function getRamlParser()
    {
        return new RamlParser(new YamlParser(), $this->getRamlNormalizer());
    }
    
    public function getRamlNormalizer()
    {
        return new Normalizer\RecursiveNormalizer($this->getNormalizers());
    }

    public function getRamlBuilder()
    {
        $resolver = $this->getTypeResolver();

        return new Builder(new TypeConstructor($resolver), $resolver);
    }

    public function getTypeResolver()
    {
        return new TypeRegistry();
    }

    protected function getNormalizers()
    {
        $normalizers = [
            new Normalizer\TypesNormalizer(
                new TypesMapFactory()
            ),
            new Normalizer\MethodCollectionNormalizer(),
            new Normalizer\ResponseCollectionNormalizer(),
            new Normalizer\ResourceCollectionNormalizer(),
            new Normalizer\AnnotationsNormalizer(),
            new Normalizer\BodyNormalizer(),
            new Normalizer\HeadersNormalizer(),
            new Normalizer\ResponseNormalizer(),
            new Normalizer\UriNormalizer(),
            new Normalizer\ResourceNormalizer(),
            new Normalizer\RamlNormalizer(),
        ];
        
        $builder = $this->getRamlBuilder();
        return array_map(function (Normalizer\Normalizer $normalizer) use ($builder) {
            if ($normalizer instanceof RamlBuilderAware) {
                $normalizer->setRamlBuilder($builder);
            }

            return $normalizer;
        }, $normalizers);
    }
}