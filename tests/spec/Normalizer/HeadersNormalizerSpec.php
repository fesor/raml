<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Builder;
use Fesor\RAML\Type\ObjectType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HeadersNormalizerSpec extends ObjectBehavior
{
    function let(Builder $builder)
    {
        $this->setRamlBuilder($builder);
    }

    function it_supports_only_headers_node(Builder $builder)
    {
        $builder->createType()->shouldNotBeCalled();
        $this->normalize([], ['foo'])->shouldReturn([]);
    }
    
    function it_converts_headers_to_object_type(Builder $builder)
    {
        $headers = new ObjectType([], []);
        $builder->createType([
            'properties' => ['headers']
        ])->willReturn($headers)->shouldBeCalled();
        $this->normalize(['headers'], ['headers'])->shouldReturn($headers);
    }
}
