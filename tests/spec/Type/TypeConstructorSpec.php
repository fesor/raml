<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Normalizer\TypeNormalizer;
use Fesor\RAML\Type\ArrayType;
use Fesor\RAML\Type\BooleanType;
use Fesor\RAML\Type\NullType;
use Fesor\RAML\Type\NumberType;
use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\StringType;
use Fesor\RAML\Type\Type;
use Fesor\RAML\Type\TypeResolver;
use Fesor\RAML\Type\UnionType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeConstructorSpec extends ObjectBehavior
{
    function let(TypeResolver $resolver)
    {
        $this->beConstructedWith($resolver);
    }

    function it_supports_string_types()
    {
        $this->createType(['type' => 'string'])->shouldReturnAnInstanceOf(StringType::class);
    }

    function it_supports_objects()
    {
        $this->createType([
            'properties' => [
                'foo' => 'string',
                'bar?' => 'string',
                'buz?' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ])->shouldBeLike(new ObjectType([], [
            'foo' => new StringType([]),
            'bar' => new StringType(['required' => false]),
            'buz?' => new StringType(['required' => true]),
        ]));
    }

    function it_supports_pattern_properties_for_object()
    {
        $this->createType([
            'properties' => [
                'foo' => 'string',
                '/^node\-\d+$/' => 'number'
            ]
        ])->shouldBeLike(new ObjectType([
            'patternProperties' => [
                '/^node\-\d+$/' => new NumberType()
            ]
        ], [
            'foo' => new StringType([]),
        ]));
    }

    function it_supports_arrays()
    {
        $this->createType(['type' => 'array', 'items' => 'string'])
            ->shouldBeLike(new ArrayType([
                'items' => new StringType([])
            ]));
    }

    function it_supports_union_types()
    {
        $this->createType(['type' => 'string | number'])->shouldBeLike(new UnionType([
            new NumberType([]), new StringType([])
        ]));
    }

    function it_creates_type_declaration_from_type_expression()
    {
        $this->createType('string')->shouldReturnAnInstanceOf(StringType::class);
        $this->createType('null')->shouldReturnAnInstanceOf(NullType::class);
        $this->createType('boolean')->shouldReturnAnInstanceOf(BooleanType::class);
    }
}
