<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RootNormalizerSpec extends ObjectBehavior
{

    function let(Normalizer $typeNormalizer)
    {
        $this->beConstructedWith($typeNormalizer);
    }

    function it_collects_resources()
    {
        $this->normalize([
            'title' => 'API',
            '/' => [],
            '/users' => ['description' => '']
        ])->shouldBeLike([
            'title' => 'API',
            'resources' => [
                [
                    'uri' => '/',
                    'resources' => []
                ],
                [
                    'uri' => '/users',
                    'description' => '',
                    'resources' => []
                ]
            ]
        ]);
    }

    function it_normalizes_nested_resource_definitions()
    {
        $this->normalize([
            '/users' => [
                '/{id}' => [
                    'description' => 'user details'
                ]
            ]
        ])->shouldBeLike([
            'resources' => [
                [
                    'uri' => '/users',
                    'resources' => [
                        [
                            'description' => 'user details',
                            'uri' => '/{id}',
                            'resources' => []
                        ]
                    ]
                ],
            ]
        ]);
    }

    function it_normalizes_annotation_types_using_type_normalizer(Normalizer $typeNormalizer)
    {

        $this->shouldNormalizeTypes($typeNormalizer, 'null | string', null, [
                'properties' => [
                    'level' => [
                        'enum' => ['low', 'medium', 'high']
                    ]
                ]
            ]
        );

        $this->normalize([
            'annotationTypes' => [
                'experimental' => 'null | string',
                'badge' => null,
                'clearanceLevel' => [
                    'properties' => [
                        'level' => [
                            'enum' => ['low', 'medium', 'high']
                        ]
                    ]
                ]
            ]
        ])->shouldBeLike([
            'resources' => [],
            'annotationTypes' => [
                'experimental' => 'tested',
                'badge' => 'tested',
                'clearanceLevel' => 'tested'
            ],
        ]);
    }

    private function shouldNormalizeTypes($typeNormalizer, ...$types)
    {
        foreach ($types as $type) {
            $typeNormalizer->normalize(Argument::exact($type))
                ->willReturn('tested')
                ->shouldBeCalled();
        }
    }
}
