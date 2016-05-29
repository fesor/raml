<?php

namespace Fesor\RAML\Type;

class ObjectType extends Type
{
    private $properties;

    public function __construct(array $facets, array $properties = [])
    {
        $this->properties = $properties;
        parent::__construct($facets);
    }

    public function knownFacets()
    {
        return $this->extendKnownFacets([
            'properties',
            'patternProperties',
            'minProperties',
            'maxProperties',
            'additionalProperties',
            'discriminator',
            'discriminatorValue',
        ]);
    }

    public function properties()
    {
        $properties = $this->properties;
        if (!$this->baseType) {
            return $properties;
        }

        $baseType = $this->baseType;
        if (!is_array($baseType)) {
            $baseType = [$baseType];
        }
        $baseProperties = array_map(function (ObjectType $baseType) {
            return $baseType->properties();
        }, $baseType);

        return array_merge($properties, ...$baseProperties);
    }

    public function patternProperties()
    {
        return $this->facets['patternProperties'];
    }

    public function minProperties()
    {
        return $this->facets['minProperties'];
    }

    public function maxProperties()
    {
        return $this->facets['maxProperties'];
    }

    public function additionalProperties()
    {
        return $this->facets['additionalProperties'];
    }

    public function discriminator()
    {
        return $this->facets['discriminator'];
    }

    public function discriminatorValue()
    {
        return $this->facets['discriminatorValue'];
    }

    protected function isValidDeclaration()
    {
        return true;
    }

    public function extend(array $facets)
    {
        $properties = isset($facets['properties']) ?
            $facets['properties'] : [];
        unset($facets['properties']);

        $subtype = parent::extend($facets);
        $subtype->properties = $properties;

        return $subtype;
    }

    public function required()
    {
        return array_keys(
            array_filter(
                array_map(function (Type $type) {
                    return $type->isRequired();
                }, $this->properties())
            )
        );
    }
}
