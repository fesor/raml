<?php

namespace spec\Fesor\RAML\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NumberTypeSpec extends ObjectBehavior
{
    use TypeSpecTrait;

    function let()
    {
        $this->fromArray([
            'type' => 'number',
            'minimum' => 4,
            'maximum' => 100,
            'format' => 'int64',
            'multipleOf' => 4,
        ]);
    }

    function it_returns_minimum_value()
    {
        $this->getMinimum()->shouldReturn(4);
    }

    function it_returns_maximum_value()
    {
        $this->getMaxium()->shouldReturn(100);
    }

    function it_returns_format()
    {
        $this->getFormat()->shouldReturn('int64');
    }

    function it_returns_multiplie_of()
    {
        $this->getMultipleOf()->shouldReturn(4);
    }
}
