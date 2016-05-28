<?php

namespace Fesor\RAML;

class Raml
{
    private $data;

    private $metadata;
    private $title;
    private $description;
    private $version;
    private $protocols;
    private $baseUri;
    private $mediaType;
    private $documentation;
    private $types;
    private $annotationTypes;
    private $traits;
    private $resourceTypes;
    private $securitySchemas;
    private $resources;

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromArray($data)
    {
        $mapping = [
            '_metadata' => 'metadata',
            'title'  => 'title',
            'description'    => 'description',
            'version'    => 'version',
            'mediaType'  => 'mediaType',
            'protocols'  => 'protocols',
            'baseUri'    => 'baseUri',
            'documentation'  => 'documentation',
            'types'  => 'types',
            'traits'     => 'traits',
            'resourceTypes'  => 'resourceTypes',
            'annotationTypes'    => 'annotationTypes',
            'securitySchemas'    => 'securitySchemas',
            'resources'  => 'resources',
        ];

        $defaults = [
            '_metadata' => ['version' => 1.0, 'type' => 'API'],
            'resources' => [],
        ];

        $data = array_replace($defaults, $data);
        $raml = new Raml($data);

        foreach ($mapping as $from => $to) {
            $raml->{$to} = isset($data[$from]) ? $data[$from] : null;
        }

        return $raml;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

    public function getAllResources()
    {
        return $this->collectResources($this->resources);
    }

    private function collectResources(array $resources)
    {
        $collection = [];
        foreach ($resources as $resource) {
            $collection[] = $resource;
            $collection = array_merge($collection, $this->collectResources($resource->getSubResources()));
        }

        return $collection;
    }
}
