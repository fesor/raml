<?php

namespace Fesor\RAML\Type;

class ObjectType extends Type
{
    public function knownFacets()
    {
        return array_merge(parent::knownFacets(), [
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
        return isset($this->facets['properties']) ? $this->facets['properties'] : [];
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
