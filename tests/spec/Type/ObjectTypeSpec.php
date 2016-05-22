<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\StringType;
use Prophecy\Argument;

class ObjectTypeSpec extends TypeObjectBehaviour
{
    function let()
    {
        $this->withFacets([
            'properties' => [
                'foo' => new StringType([]),
                'bar' => new StringType(['required' => false])
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
                'buz' => new StringType([])
            ]
        ])->shouldReturnExtendedType(function (ObjectType $objectType) {
            return $objectType->required() == ['foo', 'buz'];
        });
    }

    function it_not_allows_to_make_property_optional()
    {
        $this->extend([
            'properties' => [
                'foo' => new StringType(['required' => false])
            ]
        ])->shouldReturnExtendedType(function (ObjectType $objectType) {
            return $objectType->required() == ['foo'];
        });
    }
}
