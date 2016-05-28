<?php

namespace spec\Fesor\RAML\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseCollectionNormalizerSpec extends ObjectBehavior
{
    function it_supports_only_responses_node()
    {
        $this->supports(['bar', 'responses'])->shouldReturn(true);
        $this->supports(['responses', 'foo'])->shouldReturn(false);
    }

    function it_collects_responses()
    {
        $this->normalize([
            200 => [],
            401 => []
        ])->shouldBeLike([
            ['statusCode' => 200],
            ['statusCode' => 401],
        ]);
    }
}
