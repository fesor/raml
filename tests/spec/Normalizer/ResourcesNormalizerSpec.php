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
        $this->shouldNormalizeMethod($methodNormalizer, 'post');
        $this->shouldNormalizeMethod($methodNormalizer, 'get');

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

    private function shouldNormalizeMethod($methodNormalizer, $method)
    {
        $methodNormalizer->normalize(compact('method'))->willReturn(['method'])->shouldBeCalled();
    }
}
