<?php

namespace spec\Fesor\RAML;

use Fesor\RAML\Type\NumberType;
use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Uri;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UriSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith('/users/{id}', new ObjectType([
            'id' => new NumberType([])
        ]));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Fesor\RAML\Uri');
    }

    function it_returns_absolute_uri()
    {
        $this->absoluteUri()->shouldReturn('/users/{id}');
    }

    function it_returns_absolute_uri_with_base_uri()
    {
        $this->beConstructedWith('/{id}', new ObjectType([]), new Uri('/users', new ObjectType([])));
        $this->absoluteUri()->shouldReturn('/users/{id}');
    }

    function it_returns_parameters()
    {
        $this->parameters()->shouldReturnAnInstanceOf(ObjectType::class);
    }

    function it_returns_uri_template()
    {
        $this->uriTemplate()->shouldReturn('/users/{id}');
    }
}
