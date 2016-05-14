<?php

namespace spec\Fesor\RAML\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RootNormalizerSpec extends ObjectBehavior
{
    function it_collects_resources()
    {
        $this->normalize([
            'title' => 'API',
            '/' => [],
            '/users' => ['description' => '']
        ])->shouldBeLike([
            'title' => 'API',
            'resources' => [
                [
                    'uri' => '/',
                    'resources' => []
                ],
                [
                    'uri' => '/users',
                    'description' => '',
                    'resources' => []
                ]
            ]
        ]);
    }

    function it_normalizes_nested_resource_definitions()
    {
        $this->normalize([
            '/users' => [
                '/{id}' => [
                    'description' => 'user details'
                ]
            ]
        ])->shouldBeLike([
            'resources' => [
                [
                    'uri' => '/users',
                    'resources' => [
                        [
                            'description' => 'user details',
                            'uri' => '/{id}',
                            'resources' => []
                        ]
                    ]
                ],
            ]
        ]);
    }
}
