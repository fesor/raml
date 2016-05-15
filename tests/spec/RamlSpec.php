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
        $this->fromArray([
            'resources' => [
                [
                    'uri' => '/users',
                    'resources' => [
                        [
                            'uri' => '/{id}',
                            'resources' => []
                        ]
                    ]
                ]
            ]
        ]);
        $this->getAllResources()->shouldContainResources([
            '/users', '/users/{id}',
        ]);
    }

    private function fromArray($raml) {
        $this->beConstructedThrough('fromArray', [$raml]);
    }

    public function getMatchers()
    {
        return [
            'containResources' => function ($subject, $uris) {
                $actualUris = array_map(function (Resource $resource) {
                    return $resource->getAbsoluteUri();
                }, $subject);

                return $actualUris == $uris;
            }
        ];
    }
}
