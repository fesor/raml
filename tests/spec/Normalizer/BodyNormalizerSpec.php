<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\TypeConstructor;
use Fesor\RAML\Type\TypeResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BodyNormalizerSpec extends ObjectBehavior
{
    function let(TypeConstructor $typeConstructor)
    {
        $this->setTypeConstructor($typeConstructor);
    }

    function it_normalizes_only_body_nodes()
    {
        $this->normalize('CustomType', ['not-body'])->shouldReturn('CustomType');
    }

    function it_normalizes_body(TypeConstructor $typeConstructor)
    {
        $this->shouldConstructCustomType($typeConstructor);

        $this->normalize([
            'application/json' => 'CustomType'
        ], ['body']);
    }
    
    function it_normalizes_body_declared_as_just_type_expression(TypeConstructor $typeConstructor)
    {
        $this->shouldConstructCustomType($typeConstructor);

        $this->normalize('CustomType', ['body']);
    }
    
    function it_allows_type_expressions_as_values_for_media_type_map(TypeConstructor $typeConstructor)
    {
        $this->shouldConstructCustomType($typeConstructor);

        $this->normalize([
            'application/json' => [
                'type' => 'CustomType'
            ],
        ], ['body']);
    }

    function it_uses_default_media_type_if_not_specified(TypeConstructor $typeConstructor)
    {
        $this->normalize(['mediaType' => 'text/xml'], []);
        $this->shouldConstructCustomType($typeConstructor);
        $this->normalize('CustomType', ['body']);
    }

    private function shouldConstructCustomType(TypeConstructor $typeConstructor)
    {
        $typeConstructor
            ->createType('CustomType', null)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();
    }
}
