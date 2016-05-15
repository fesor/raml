<?php

namespace spec\Fesor\RAML\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StringTypeSpec extends ObjectBehavior
{
    use TypeSpecTrait;

    function it_supports_patterns()
    {
        $this->fromArray([
            'pattern' => '^\d+\-\w+$'
        ]);

        $this->getPattern()->shouldReturn('^\d+\-\w+$');
    }

    function it_supports_min_length()
    {
        $this->fromArray([
            'minLength' => 1
        ]);

        $this->getMinLength()->shouldReturn(1);
    }

    function it_supports_max_length()
    {
        $this->fromArray([
            'maxLength' => 128
        ]);

        $this->getMaxLength()->shouldReturn(128);
    }
}
