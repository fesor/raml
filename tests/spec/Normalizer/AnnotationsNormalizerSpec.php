<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\AnnotationRef;
use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnnotationsNormalizerSpec extends ObjectBehavior
{
    function it_supports_only_inward_processing()
    {
        $this->supportsDirection()->shouldReturn(Normalizer::DIRECTION_INWARD);
    }

    function it_collects_annotations_for_every_node_extept_annotations()
    {
        $this->supports(['anything'])->shouldReturn(true);
        $this->supports(['annotations'])->shouldReturn(false);
    }

    function it_has_top_priority()
    {
        $this->priority()->shouldReturn(50);
    }

    function it_collects_annotations()
    {
        $this->normalize([
            'value' => 'test',
            '(annotation1)' => 'value1',
            '(annotation2)' => 'value2'
        ], [])->shouldBeLike([
            'annotations' => [
                new AnnotationRef('annotation1', 'value1'),
                new AnnotationRef('annotation2', 'value2')
            ],
            'value' => 'test'
        ]);
    }
}
