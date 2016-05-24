<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecursiveNormalizerSpec extends ObjectBehavior
{
    function let(Normalizer $first, Normalizer $second)
    {
        $first->supports(Argument::any())->willReturn(false);
        $second->supports(Argument::any())->willReturn(false);

        $this->beConstructedWith([$first, $second]);
    }

    function it_traverse_arrays_recursivly_and_applies_normalizer_to_nodes(
        Normalizer $first,
        Normalizer $second
    )
    {
        $first->supportsDirection()->willReturn(Normalizer::DIRECTION_INWARD);
        $second->supportsDirection()->willReturn(Normalizer::DIRECTION_OUTWARD);

        $first->supports(['foo'])
            ->willReturn(true)
            ->shouldBeCalled();
        $first->normalize(['bar' => [], 'buz' => 'scalar'])
            ->willReturn(['buz' => 'scalar'])
            ->shouldBeCalled();

        $second->supports(['foo'])->willReturn(true)->shouldBeCalled();
        $second->normalize(['buz' => 'scalar'])->willReturnArgument(0)->shouldBeCalled();

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

        $first->supports(Argument::any())->willReturn(true);
        $second->supports(Argument::any())->willReturn(true);

        $first->normalize(['foo' => 'first'])
            ->willReturn(['foo' => 'second'])
            ->shouldBeCalled();
        $second->normalize(['foo' => 'second'])
            ->willReturnArgument(0)
            ->shouldBeCalled();

        $this->normalize([
            'foo' => 'first'
        ]);
    }
}
