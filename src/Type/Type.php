<?php


namespace Fesor\RAML\Type;


abstract class Type
{
    protected $facets;
    protected $userDefinedFacets;
    protected $annotations;
    protected $baseType;

    public function __construct($facets)
    {
        $keys = $this->knownFacets();
        $defaultValues = array_fill(0, count($keys), null);
        $defaults = array_combine($keys, $defaultValues);

        $this->facets = array_replace(
            $defaults,
            array_intersect_key($facets, $defaults)
        );
    }

    protected function knownFacets()
    {
        return [
            'description',
            'displayName',
            'required'
        ];
    }

    public function displayName()
    {
        return (string) $this->facets['displayName'];
    }

    public function description()
    {
        return (string) $this->facets['description'];
    }

    public function isRequired()
    {
        return false !== $this->facets['required'];
    }

    public function extend(array $facets)
    {
        $extendedType = clone $this;
        $extendedType->facets = self::deepMerge($extendedType->facets, $facets);
        $extendedType->baseType = $this;

        if (!$extendedType->isValidDeclaration()) {
            throw new \RuntimeException('Invalid type declaration');
        }

        return $extendedType;
    }

    protected function isValidDeclaration()
    {
        return true;
    }

    private static function deepMerge(array $a, array $b)
    {
        $intersection = array_intersect_key($a, $b);
        $result = array_replace($a, $b);

        if (array_key_exists('required', $a)) {
            $result['required'] = $a['required'];
        }

        foreach ($intersection as $key => $value) {

            if ($value instanceof Type && $b[$key] instanceof Type && !empty($b[$key]->facets)) {
                $result[$key] = $value->extend($b[$key]->facets);
            }

            if (!is_array($value)) continue;

            if (array_keys($value) === range(0, count($value))) {
                $result[$key] = array_merge($a[$key], $b[$key]);
            } else {
                $result[$key] = self::deepMerge($a[$key], $b[$key]);
            }
        }

        return $result;
    }
}