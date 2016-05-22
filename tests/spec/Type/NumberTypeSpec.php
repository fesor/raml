<?php

namespace spec\Fesor\RAML\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NumberTypeSpec extends TypeObjectBehaviour
{
    function let()
    {
        $this->withFacets([
            'minimum' => 0,
            'maximum' => 100,
            'multipleOf' => 1,
            'format' => 'int32'
        ]);
    }

    function it_returns_minimum_value()
    {
        $this->minimum()->shouldReturn(0);
    }

    function it_returns_maximum_value()
    {
        $this->maximum()->shouldReturn(100);
    }

    function it_returns_format()
    {
        $this->format()->shouldReturn('int32');
    }

    function it_returns_multiple_of()
    {
        $this->multipleOf()->shouldReturn(1);
    }

    function it_validates_number_value()
    {
        $this->validateValue(1)->shouldBeLike([]);
        $this->validateValue(-10)->shouldBeLike(['minimum']);
        $this->validateValue(101)->shouldBeLike(['maximum']);
        $this->validateValue(1.34)->shouldBeLike(['multipleOf']);
    }
}
