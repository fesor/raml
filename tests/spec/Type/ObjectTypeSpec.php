<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\PropertyItem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ObjectTypeSpec extends TypeObjectBehaviour
{
    function let()
    {
        $this->withFacets([
            'properties' => [
                'foo' => new PropertyItem(null, true),
                'bar' => new PropertyItem(null, false)
            ],
        ]);
    }

    function it_returns_list_of_required_properties()
    {
        $this->required()->shouldBeLike(['foo']);
    }

    function it_allows_to_extend_with_additional_properties()
    {
        $this->extend([
            'properties' => [
                'buz' => new PropertyItem(null, true)
            ]
        ])->shouldReturnExtendedType(function (ObjectType $objectType) {
            return $objectType->required() == ['foo', 'buz'];
        });
    }

    function it_allows_to_make_property_optional()
    {
        $this->extend([
            'properties' => [
                'foo' => new PropertyItem(null, false)
            ]
        ])->shouldReturnExtendedType(function (ObjectType $objectType) {
            return $objectType->required() == [];
        });
    }
}
