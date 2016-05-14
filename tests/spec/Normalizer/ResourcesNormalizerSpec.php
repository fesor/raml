<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourcesNormalizerSpec extends ObjectBehavior
{
    function let(Normalizer $methodNormalizer)
    {
        $this->beConstructedWith($methodNormalizer);
    }

    function it_collects_methods(Normalizer $methodNormalizer)
    {
        $this->normalizerShouldBeCalledTimes($methodNormalizer, 2);
        
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
                ['method'],
                ['method']
            ],
        ]);
    }

    private function normalizerShouldBeCalledTimes($methodNormalizer, $n)
    {
        $methodNormalizer->normalize(Argument::that(function ($arg) {
            return isset($arg['method']);
        }))->willReturn(['method'])->shouldBeCalledTimes($n);
    }
}
