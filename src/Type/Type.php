<?php

namespace Fesor\RAML\Type;

use function Fesor\RAML\withDefaultValues;

abstract class Type
{
    private $name;

    private $data;

    protected $parentType;

    /**
     * Type constructor.
     * @param string $name
     * @param array $data
     * @param Type|null $parentType
     */
    protected function __construct($name, array $data, Type $parentType = null)
    {
        $this->data = withDefaultValues([
            'type' => 'string',
            'example' => null,
            'examples' => [],
            'displayName' => $name,
            'description' => '',
            'annotations' => [],
            'facets' => []
        ], $data);

        $this->parentType = $parentType;
    }

    public function getDisplayName()
    {
        return $this->data['displayName'];
    }

    public function getDescription()
    {
        return $this->data['description'];
    }

    public function getExample()
    {
        return $this->data['example'];
    }

    public function getExamples()
    {
        return $this->data['examples'];
    }

    public function getBaseType()
    {
        return $this->parentType;
    }

    public function getUserDefinedFacets()
    {
        return $this->data['facets'];
    }

    public function getAnnotations()
    {
        return $this->data['annotations'];
    }
}