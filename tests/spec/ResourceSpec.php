<?php

namespace spec\Fesor\RAML;

use Fesor\RAML\Resource;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourceSpec extends ObjectBehavior
{

    function it_returns_display_name()
    {
        $this->fromArray(['uri' => '/users/{id}', 'displayName' => 'user details']);
        $this->getDisplayName()->shouldReturn('user details');
    }

    function it_returns_uri_as_default_value_for_display_name()
    {
        $this->fromArray(['uri' => '/users/{id}']);
        $this->getDisplayName()->shouldReturn('/users/{id}');
    }

    function it_returns_description()
    {
        $this->fromArray(['description' => 'description']);
        $this->getDescription()->shouldReturn('description');
    }

    function it_returns_sub_resources()
    {
        $this->fromArray([]);
        $this->getSubResources()->shouldReturn([]);
    }

    function it_returns_uri(Resource $parent)
    {
        $this->fromArray(['uri' => '/{id}'], $parent);
    }

    function it_returns_absolute_uri(Resource $parent)
    {
        $parent->getUri()->willReturn('/users');
        $this->fromArray(['uri' => '/{id}'], $parent);

        $this->getAbsoluteUri()->shouldReturn('/users/{id}');
    }

    private function fromArray($fragment, Resource $parent = null)
    {
        $this->beConstructedThrough('fromArray', [$fragment, $parent]);
    }
}
