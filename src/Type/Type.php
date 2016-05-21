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

    public function extend(array $facets)
    {
        $extendedType = clone $this;
        $extendedType->facets = array_replace($extendedType->facets, $facets);
        $extendedType->baseType = $this;

        return $extendedType;
    }
}