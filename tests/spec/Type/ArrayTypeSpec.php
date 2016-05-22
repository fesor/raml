<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\StringType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayTypeSpec extends TypeObjectBehaviour
{
    function let()
    {
        $this->withFacets([
            'items' => new StringType(['minLength']),
            'minItems' => 1,
            'maxItems' => 10
        ]);
    }

    function it_validates_value()
    {
        $this->validateValue(['foo', 'bar'])->shouldBeLike([]);
        $this->validateValue([])->shouldBeLike(['minItems']);
        $this->validateValue(range('a', 'z'))->shouldBeLike(['maxItems']);
    }
}
