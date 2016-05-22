<?php

namespace Fesor\RAML\Type;

class NumberType extends Type
{
    protected function knownFacets()
    {
        return array_merge(parent::knownFacets(), [
            'minimum',
            'maximum',
            'format',
            'multipleOf'
        ]);
    }

    public function minimum()
    {
        return $this->facets['minimum'];
    }

    public function maximum()
    {
        return $this->facets['maximum'];
    }

    public function format()
    {
        return $this->facets['format'];
    }

    public function multipleOf()
    {
        return $this->facets['multipleOf'];
    }

    protected function isValidDeclaration()
    {
        return $this->minimum() <= $this->maximum();
    }

    public function validateValue($value)
    {
        $errors = [
            'minimum' => null !== $this->minimum() && $value < $this->minimum(),
            'maximum' => null !== $this->maximum() && $value > $this->maximum(),
            'multipleOf' => null !== $this->multipleOf() && fmod($value, $this->multipleOf()) !== 0.0
        ];

        return array_keys(array_filter($errors));
    }
}
