<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PropertiesDeclarationNormalizerSpec extends ObjectBehavior
{
    function let(Normalizer $typeNormalizer)
    {
        $this->beConstructedWith($typeNormalizer);
    }

    function it_normalizes_properties_declaration_using_type_normalizer(Normalizer $typeNormalizer)
    {
        $this->shouldNormalizesTypes($typeNormalizer);

        $this->normalize([
            'X-Authorization' => [
                'pattern' => '^Bearer .+'
            ],
            'X-Other-Header?' => 'string',
        ])
            ->shouldReturn('normalized');
    }

    private function shouldNormalizesTypes($normalizer)
    {
        $normalizer->normalize(Argument::any())->willReturn([
            'type' => 'object',
            'properties' => 'normalized'
        ])->shouldBeCalled();
    }
}
