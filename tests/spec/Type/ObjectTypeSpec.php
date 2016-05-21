<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\PropertyItem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ObjectTypeSpec extends TypeObjectBehaviour
{
    function let()
    {
        $this->withFacets([
            'properties' => [
                'foo' => new PropertyItem(null, true),
                'bar' => new PropertyItem(null, false)
            ],
        ]);
    }

    function it_returns_list_of_required_properties()
    {
        $this->required()->shouldBeLike(['foo']);
    }
}
