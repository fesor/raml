<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\StringType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StringTypeSpec extends TypeObjectBehaviour
{
    function let()
    {
        $this->withFacets([
            'pattern' => 'pattern',
            'minLength' => 7,
            'maxLength' => 10
        ]);
    }

    function it_returns_description()
    {
        $this->withFacets(['description' => 'test description']);
        $this->description()->shouldReturn('test description');
    }

    function it_returns_display_name()
    {
        $this->withFacets(['displayName' => 'example type']);
        $this->displayName()->shouldReturn('example type');
    }

    function it_returns_pattern()
    {
        $this->withFacets(['pattern' => 'pattern']);
        $this->pattern()->shouldReturn('/pattern/');
    }

    function it_returns_min_and_max_length()
    {
        $this->minLength()->shouldReturn(7);
        $this->maxLength()->shouldReturn(10);
    }

    function it_returns_min_and_max_length_defaults()
    {
        $this->withFacets([]);
        $this->minLength()->shouldReturn(0);
        $this->maxLength()->shouldReturn(null);
    }

    function it_extendable()
    {
        $this->extend([
            'pattern' => 'overwritten'
        ])->shouldReturnExtendedType(function (StringType $type) {
            return $type->pattern() === '/overwritten/';
        });
    }

    function it_returns_list_of_validation_errors()
    {
        $this->validateValue('pattern ok')->shouldBeLike([]);
        $this->validateValue('pattern too long')->shouldBeLike(['maxLength']);
        $this->validateValue('short')->shouldBeLike(['minLength', 'pattern']);
        $this->validateValue('invalid')->shouldBeLike(['pattern']);
    }
}
