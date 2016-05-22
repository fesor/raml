<?php

namespace Fesor\RAML\Type;

class StringType extends Type
{
    protected function knownFacets()
    {
        return array_merge(parent::knownFacets(), [
            'pattern',
            'minLength',
            'maxLength',
        ]);
    }

    protected function isValidEnum(array $enum)
    {
        return 0 === array_filter($enum, function ($val) {
            return !(null === $val || is_string($val));
        });
    }

    public function pattern()
    {
        if (null === $this->facets['pattern']) {
            return null;
        }

        return sprintf('/%s/', $this->facets['pattern']);
    }

    public function minLength()
    {
        return (int) $this->facets['minLength'];
    }

    public function maxLength()
    {
        return isset($this->facets['maxLength']) ?
            (int) $this->facets['maxLength'] : null;
    }

    protected function isValidDeclaration()
    {
        return $this->minLength() <= $this->maxLength();
    }

    public function validateValue($value)
    {
        $errors = [
            'maxLength' => null !== $this->maxLength() && mb_strlen($value) > $this->maxLength(),
            'minLength' => mb_strlen($value) < $this->minLength(),
            'pattern' => null !== $this->pattern() && !preg_match($this->pattern(), $value)
        ];

        return array_keys(array_filter($errors));
    }


}