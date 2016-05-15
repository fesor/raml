<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\TypeRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ScalarTypeSpec extends ObjectBehavior
{
    use TypeSpecTrait;

    function let()
    {
        $this->fromArray([
            'description' => 'example description',
            'enum' => ['foo', 'bar']
        ]);
    }


    function it_returns_description()
    {
        $this->getDescription()->shouldReturn('example description');
    }

    function it_returns_enum_values()
    {
        $this->getEnum()->shouldBeLike(['foo', 'bar']);
    }

}
