<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Body;
use Fesor\RAML\Normalizer\Normalizer;
use Fesor\RAML\Response;
use Fesor\RAML\Type\ObjectType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseNormalizerSpec extends ObjectBehavior
{
    function it_supports_only_outward_direction()
    {
        $this->supportsDirection()->shouldReturn(Normalizer::DIRECTION_OUTWARD);
    }

    function it_supports_only_responses_nodes()
    {
        $this->normalize(['foo'], [])->shouldReturn(['foo']);
    }

    function it_normalizes_response_node()
    {
        $this->normalize([
            [
                'statusCode' => 200,
                'body' => [new Body('application/json', new ObjectType([]))],
                'headers' => new ObjectType([])
            ]
        ], ['foo', 'responses'])->shouldReturnCollectionOfType(Response::class);
    }
}
