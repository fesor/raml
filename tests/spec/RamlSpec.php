<?php

namespace spec\Fesor\RAML;

use Fesor\RAML\Resource;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RamlSpec extends ObjectBehavior
{
    function it_returns_title()
    {
        $this->fromArray(['title' => 'My Api']);
        $this->getTitle()->shouldReturn('My Api');
    }

    function it_returns_description()
    {
        $this->fromArray(['description' => 'My Api Description']);
        $this->getDescription()->shouldReturn('My Api Description');
    }

    function it_returns_api_version()
    {
        $this->fromArray(['version' => 1]);
        $this->getVersion()->shouldReturn(1);
    }

    function it_returns_all_available_resources()
    {
        $parentResource = Resource::fromArray([]);
        $childResource = Resource::fromArray([]);
        $parentResource->addSubResource($childResource);

        $this->fromArray([
            'resources' => [$parentResource]
        ]);
        $this->getAllResources()->shouldBeLike([
            $parentResource,
            $childResource
        ]);
    }

    private function fromArray($raml) {
        $this->beConstructedThrough('fromArray', [$raml]);
    }
}
