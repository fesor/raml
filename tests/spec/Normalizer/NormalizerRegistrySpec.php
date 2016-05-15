<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\TypeNormalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NormalizerRegistrySpec extends ObjectBehavior
{
    function let()
    {
        $this->addNormalizer('type', new TypeNormalizer());
    }

    function it_resolves_normalizer_for_given_name()
    {
        $this->shouldNotThrow()->duringGetNormalizer('type');
        $this->shouldThrow()->duringGetNormalizer('unknown');
    }
}
