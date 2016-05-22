<?php

namespace Fesor\RAML\Type;

class ArrayType extends Type
{
    protected function knownFacets()
    {
        return array_merge(parent::knownFacets(), [
            'items',
            'minItems',
            'maxItems',
            'uniqueItems'
        ]);
    }

    /**
     * @return Type|null
     */
    public function items()
    {
        return $this->facets['type'];
    }

    public function minItems()
    {
        return (int) $this->facets['minItems'];
    }

    public function maxItems()
    {
        return isset($this->facets['maxItems']) ?
            (int) $this->facets['maxItems'] : 2147483647;
    }

    public function uniqueItems()
    {
        return (bool) $this->facets['uniqueItems'];
    }

    public function validateValue($value)
    {
        $errors = [
            'items' => false,
            'minItems' => count($value) < $this->minItems(),
            'maxItems' => count($value) > $this->maxItems(),
            'uniqueItems' => false
        ];

        return array_keys(array_filter($errors));
    }

}
