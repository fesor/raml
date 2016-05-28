<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourceCollectionNormalizerSpec extends ObjectBehavior
{
    function it_supports_only_inward_processing()
    {
        $this->supportsDirection()->shouldReturn(Normalizer::DIRECTION_INWARD);
    }

    function it_should_have_high_priority()
    {
        $this->priority()->shouldReturn(10);
    }

    function it_supports_only_root_and_resource_type_node()
    {
        $this->supports([])->shouldReturn(true);
        $this->supports(['foo'])->shouldReturn(false);
        $this->supports(['resourceTypes'])->shouldReturn(false);
        $this->supports(['resourceTypes', 'foo'])->shouldReturn(true);
    }

    function it_recursivly_process_all_child_nodes_and_collects_resources()
    {
        $this->normalize([
            '/foo' => [
                'foo' => 'foo',
                '/bar' => [
                    'bar' => 'bar',
                    '/buz' => [
                        'buz' => 'buz',
                    ]
                ]
            ]
        ], [])->shouldBeLike([
            'resources' => [
                [
                    'uri' => '/foo',
                    'foo' => 'foo',
                    'resources' => [
                        [
                            'uri' => '/bar',
                            'bar' => 'bar',
                            'resources' => [
                                [
                                    'uri' => '/buz',
                                    'buz' => 'buz',
                                    'resources' => []
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ]);
    }
}
