<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Builder;
use Fesor\RAML\Type\ObjectType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BodyNormalizerSpec extends ObjectBehavior
{
    /**
     * @var Builder
     */
    private $builder;

    function let(Builder $builder)
    {
        $this->builder = $builder;
        $this->setRamlBuilder($builder);
        $builder->getDefaultMediaType()->willReturn('application/json');
    }

    function it_normalizes_only_body_nodes()
    {
        $this->normalize('CustomType', ['not-body'])->shouldReturn('CustomType');
    }

    function it_normalizes_body()
    {
        $this->shouldConstructCustomType('CustomType');

        $this->normalize([
            'application/json' => 'CustomType'
        ], ['body']);
    }

    function it_normalizes_body_declared_as_just_type_expression()
    {
        $this->shouldConstructCustomType('CustomType');
        $this->normalize('CustomType', ['body']);
    }

    function it_allows_type_expressions_as_values_for_media_type_map()
    {
        $this->shouldConstructCustomType();

        $this->normalize([
            'application/json' => [
                'type' => 'CustomType'
            ],
        ], ['body']);
    }

    function it_uses_default_media_type_if_not_specified()
    {
        $this->builder->setDefaultMediaType('text/xml')->shouldBeCalled();
        $this->normalize(['mediaType' => 'text/xml'], []);
    }

    private function shouldConstructCustomType($type = ['type' => 'CustomType'])
    {
        $this->builder
            ->createType($type, null)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();
    }
}
