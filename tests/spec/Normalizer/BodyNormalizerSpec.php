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
    }

    function it_normalizes_only_body_nodes()
    {
        $this->normalize('CustomType', ['not-body'])->shouldReturn('CustomType');
    }

    function it_normalizes_body(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $typeConstructor
            ->construct('CustomType', $resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();

        $this->normalize([
            'application/json' => 'CustomType'
        ], ['body']);
    }
    
    function it_normalizes_body_declared_as_just_type_expression(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $typeConstructor
            ->construct('CustomType', $resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();

        $this->normalize('CustomType', ['body']);
    }
    
    function it_allows_type_expressions_as_values_for_media_type_map(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $typeConstructor
            ->construct('CustomType', $resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();

        $this->normalize([
            'application/json' => [
                'type' => 'CustomType'
            ],
        ], ['body']);
    }

    function it_uses_default_media_type_if_not_specified(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $this->normalize(['mediaType' => 'text/xml'], []);

        $typeConstructor
            ->construct('CustomType', $resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();

        $this->normalize('CustomType', ['body']);
    }
}
