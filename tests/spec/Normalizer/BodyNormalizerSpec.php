<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use Fesor\RAML\Normalizer\TypeConstructorAware;
use Fesor\RAML\Type\TypeConstructor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BodyNormalizerSpec extends ObjectBehavior
{
    function let(TypeConstructor $typeConstructor)
    {
        $this->setTypeConstructor($typeConstructor);
    }

    function it_type_constructor_aware_normalizer()
    {
        $this->shouldImplement(Normalizer::class);
        $this->shouldImplement(TypeConstructorAware::class);
    }

    function it_supports_only_nodes_which_may_contain_body()
    {
        $this->supports(['foo'])->shouldReturn(false);
        $this->supports(['foo', 'bar', 'body'])->shouldReturn(true);
    }
}
