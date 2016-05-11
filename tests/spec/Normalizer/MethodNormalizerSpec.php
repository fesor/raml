<?php

namespace spec\Fesor\RAML\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MethodNormalizerSpec extends ObjectBehavior
{
    function it_collects_methods()
    {
        $this->normalize([
            'test' => 'test',
            'post' => [],
            'get' => []
        ])->shouldBeLike([
            'test' => 'test',
            'methods' => [
                ['method' => 'post'],
                ['method' => 'get']
            ]
        ]);
    }
}
