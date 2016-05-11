<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnnotationsNormalizerSpec extends ObjectBehavior
{

    function let(Normalizer $normalizer)
    {
        $this->beConstructedWith($normalizer);
        $normalizer->normalize(Argument::any())->willReturnArgument(0)->shouldBeCalled();
    }

    function it_collects_annotations()
    {
        $this->normalize([
            'value' => 'test',
            '(annotation1)' => 'value1',
            '(annotation2)' => 'value2'
        ])->shouldBeLike([
            'value' => 'test',
            'annotations' => [
                'annotation1' => 'value1',
                'annotation2' => 'value2'
            ]
        ]);
    }
}
