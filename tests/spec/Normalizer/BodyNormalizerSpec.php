<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Body;
use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\TypeConstructor;
use Fesor\RAML\Type\TypeResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BodyNormalizerSpec extends ObjectBehavior
{
    function let(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $this->setTypeConstructor($typeConstructor, $resolver);
        $this->normalize(['mediaType' => 'application/json'], []);
    }

    function it_supports_only_nodes_which_may_contain_body()
    {
        $this->supports([])->shouldReturn(true);
        $this->supports(['foo'])->shouldReturn(false);
        $this->supports(['foo', 'methods', 0])->shouldReturn(true);
        $this->supports(['foo', 'methods', 0, 'bar'])->shouldReturn(false);
        $this->supports(['foo', 'responses', 0])->shouldReturn(true);
        $this->supports(['foo', 'responses', 0, 'bar'])->shouldReturn(false);
    }

    function it_normalizes_body(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $typeConstructor
            ->construct('CustomType', $resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();

        $this->normalize([
            'body' => [
                'application/json' => 'CustomType'
            ],
        ], ['methods', 0]);
    }
    
    function it_normalizes_body_declared_as_just_type_expression(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $typeConstructor
            ->construct('CustomType', $resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();

        $this->normalize([
            'body' => 'CustomType',
        ], ['methods', 0]);
    }
    
    function it_allows_type_expressions_as_values_for_media_type_map(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $typeConstructor
            ->construct('CustomType', $resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();

        $this->normalize([
            'body' => [
                'application/json' => [
                    'type' => 'CustomType'
                ]
            ],
        ], ['methods', 0]);
    }
}
