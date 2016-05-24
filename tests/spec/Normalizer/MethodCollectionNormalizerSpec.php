<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MethodCollectionNormalizerSpec extends ObjectBehavior
{
    function it_should_be_normalizer()
    {
        $this->shouldImplement(Normalizer::class);
    }

    function it_has_middle_priority()
    {
        return $this->priority()->shouldReturn(50);
    }

    function it_supports_only_inward_processing()
    {
        $this->supportsDirection()->shouldReturn(Normalizer::DIRECTION_INWARD);
    }

    function it_supports_only_resource_nodes()
    {
        $this->supports(['foo', 'resources', 0])->shouldReturn(true);
        $this->supports(['foo', 'resources'])->shouldReturn(false);
        $this->supports(['foo'])->shouldReturn(false);
    }

    function it_collects_all_method_declarations_only_for_current_value()
    {
        $this->normalize([
            'post' => [],
            'get' => [],
            'options'=>  [],
            'head'   =>  [],
            'put'    =>  [],
            'patch'  =>  [],
            'delete' =>  [],
            'resources' => [
                [
                    'put'    =>  [],
                    'delete' =>  [],
                ]
            ]
        ])->shouldBeLike([
            'methods' => [
                ['method' => 'post'],
                ['method' => 'get'],
                ['method' => 'options'],
                ['method' => 'head'   ],
                ['method' => 'put'    ],
                ['method' => 'patch'  ],
                ['method' => 'delete' ],
            ],
            'resources' => [
                [
                    'put'    =>  [],
                    'delete' =>  [],
                ]
            ]
        ]);
    }
}
