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
        $extendedType->facets = self::deepMerge($extendedType->facets, $facets);
        $extendedType->baseType = $this;

        return $extendedType;
    }

    public static function extendFromMultipleTypes(array $facets, array $types)
    {
        if (self::isMultipleInheritanceSupported($types)) {
            throw new \RuntimeException('Multiple inheritance is not supported for this combination of types');
        }

        $className = get_class(current($types));
        $subtype = new $className($facets);
        $subtype->baseType = $types;
        foreach ($types as $type) {
            $subtype->facets = self::deepMerge($subtype->facets, $type->facets);
        }

        return $subtype;
    }

    private static function isMultipleInheritanceSupported(array $types)
    {
        if (1 !== count(array_map(function (Type $type) {
            return get_class($type);
        }, array_filter($types)))) {
            return false;
        }

        return true;
    }

    private static function deepMerge(array $a, array $b)
    {
        $intersection = array_intersect_key($b, $a);
        $result = array_replace($a, $b);
        foreach ($intersection as $key => $value) {
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