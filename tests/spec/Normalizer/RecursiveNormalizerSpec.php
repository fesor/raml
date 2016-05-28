<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecursiveNormalizerSpec extends ObjectBehavior
{
    function let(Normalizer $first, Normalizer $second)
    {
        $first->normalize(Argument::any(), Argument::any())->willReturnArgument(0);
        $second->normalize(Argument::any(), Argument::any())->willReturnArgument(0);

        $this->beConstructedWith([$first, $second]);
    }

    function it_traverse_arrays_recursivly_and_applies_normalizer_to_nodes(Normalizer $first, Normalizer $second)
    {
        $first->supportsDirection()->willReturn(Normalizer::DIRECTION_INWARD);
        $second->supportsDirection()->willReturn(Normalizer::DIRECTION_OUTWARD);

        $first->normalize(['bar' => [], 'buz' => 'scalar'], ['foo'])
            ->willReturn(['buz' => 'scalar'])
            ->shouldBeCalled();

        $this->normalize([
            'foo' => [
                'bar' => [

                ],
                'buz' => 'scalar'
            ]
        ]);
    }

    function it_uses_normalizer_priority_for_correct_order(Normalizer $first, Normalizer $second)
    {
        $first->supportsDirection()->willReturn(Normalizer::DIRECTION_INWARD);
        $second->supportsDirection()->willReturn(Normalizer::DIRECTION_INWARD);

        $first->priority()->willReturn(10)->shouldBeCalled();
        $second->priority()->willReturn(100)->shouldBeCalled();

        $first->normalize(['foo' => 'first'], [])
            ->willReturn(['foo' => 'second'])
            ->shouldBeCalled();
        $second->normalize(['foo' => 'second'], [])
            ->willReturnArgument(0)
            ->shouldBeCalled();

        $this->normalize([
            'foo' => 'first'
        ], []);
    }
}
