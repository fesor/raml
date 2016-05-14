<?php

namespace spec\Fesor\RAML\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourcesNormalizerSpec extends ObjectBehavior
{

    function it_collects_methods()
    {
        $this->normalize([
            'uri' => '/users',
            'test' => 'test',
            'post' => [],
            'get' => [],
            'resources' => []
        ])->shouldBeLike([
            'uri' => '/users',
            'test' => 'test',
            'resources' => [],
            'methods' => [
                ['method' => 'post'],
                ['method' => 'get']
            ],
        ]);
    }
}
