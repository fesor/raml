<?php

namespace Fesor\RAML;

class Raml
{
    private $data;

    private function __construct(array $data)
    {
        $this->data = array_replace([
            'title' => '',
            'description' => '',
            'version' => null,
            'resources' => [],
            'baseUri' => '',
        ], $data);

        $this->data['resources'] = Resource::collectionFromArray($this->data['resources']);
    }

    public static function fromArray($data)
    {
        return new Raml($data);
    }

    public function getTitle()
    {
        return $this->data['title'];
    }

    public function getDescription()
    {
        return $this->data['description'];
    }

    public function getVersion()
    {
        return $this->data['version'];
    }

    public function getBaseUri()
    {
        return $this->data['baseUri'];
    }

    public function getAllResources()
    {
        $collection = [];
        $this->collectResources($collection, $this->data['resources']);

        return $collection;
    }

    private function collectResources(array &$collection, array $resources)
    {
        foreach ($resources as $resource) {
            $collection[] = $resource;
            $this->collectResources($collection, $resource->getSubResources(), $resources);
        }
    }
}
