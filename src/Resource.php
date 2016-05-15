<?php

namespace Fesor\RAML;

class Resource
{
    private $data;

    private $parent;

    private function __construct(array $data, Resource $parent = null)
    {
        $this->parent = $parent;
        $this->data = array_replace([
            'description' => '',
            'resources' => []
        ], $data);

        $this->data['resources'] = static::collectionFromArray($this->data['resources'], $this);
    }

    public static function fromArray(array $data, Resource $parent = null)
    {
        return new Resource($data, $parent);
    }

    public static function collectionFromArray(array $collection, Resource $parent = null)
    {
        return array_map(function (array $resource) use ($parent) {
            return static::fromArray($resource, $parent);
        }, $collection);
    }

    public function getDisplayName()
    {
        return empty($this->data['displayName']) ?
            $this->data['uri'] : $this->data['displayName'];
    }

    public function getDescription()
    {
        return $this->data['description'];
    }

    public function getUri()
    {
        return $this->data['uri'];
    }

    public function getAbsoluteUri()
    {
        $chunks = [];
        $resource = $this;
        do {
            $chunks[] = $resource->getUri();
        } while ($resource = $resource->parent);

        return implode(array_reverse($chunks));
    }

    public function getSubResources()
    {
        return $this->data['resources'];
    }
}
