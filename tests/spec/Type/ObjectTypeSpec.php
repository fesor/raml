<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\StringType;
use Prophecy\Argument;

class ObjectTypeSpec extends TypeObjectBehaviour
{
    function let()
    {
        $this->beConstructedWith([], [
            'foo' => new StringType([]),
            'bar' => new StringType(['required' => false])
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
            return $objectType->required() == ['buz', 'foo'];
        });
    }

    function it_does_not_allow_to_override_required_facet_in_subtypes()
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
