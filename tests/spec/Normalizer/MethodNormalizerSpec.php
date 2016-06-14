<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Method;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MethodNormalizerSpec extends ObjectBehavior
{
    function it_normalizes_method()
    {
        $this
            ->normalize([
                'method' => 'post'
            ], ['methods', 0])
            ->shouldReturnAnInstanceOf(Method::class);
    }

    function it_normalizes_only_collection_of_methods()
    {
        $this
            ->normalize(['method' => 'post'], ['methods'])
            ->shouldBeLike(['method' => 'post']);
        $this
            ->normalize(['method' => 'post'], ['methods', 0, 'foo'])
            ->shouldBeLike(['method' => 'post']);
    }
}
