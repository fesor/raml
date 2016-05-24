<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RamlNormalizerSpec extends ObjectBehavior
{
    function it_supports_only_root_node()
    {
        $this->supports([])->shouldReturn(true);
        $this->supports(['inside'])->shouldReturn(false);
    }

    function it_supports_only_outward_processing()
    {
        $this->supportsDirection()->shouldReturn(Normalizer::DIRECTION_OUTWARD);
    }

    function it_has_least_priority()
    {
        $this->priority()->shouldReturn(1000);
    }
}
