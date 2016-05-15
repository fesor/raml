<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use Fesor\RAML\Normalizer\NormalizerRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RootNormalizerSpec extends ObjectBehavior
{

    function let(NormalizerRegistry $normalizers)
    {
        $this->beConstructedWith($normalizers);
    }

    function it_collects_resources()
    {
        $this->normalize([
            'title' => 'API',
            '/' => [],
            '/users' => ['description' => '']
        ])->shouldBeArray([
            [
                'uri' => '/',
                'resources' => []
            ],
            [
                'uri' => '/users',
                'description' => '',
                'resources' => []
            ]
        ], ['at' => 'resources']);
    }

    function it_normalizes_nested_resource_definitions()
    {
        $this->normalize([
            '/users' => [
                '/{id}' => [
                    'description' => 'user details'
                ]
            ]
        ])->shouldBeArray([
            'uri' => '/users',
            'resources' => [
                [
                    'description' => 'user details',
                    'uri' => '/{id}',
                    'resources' => []
                ]
            ]
        ], ['at' => 'resources/0']);
    }

    function it_normalizes_annotation_types_using_type_normalizer(NormalizerRegistry $normalizers, Normalizer $typeNormalizer)
    {

        $this->shouldAskForNormalizer($normalizers, $typeNormalizer, 'type');
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
        ])->shouldBeArray([
            'experimental' => 'tested',
            'badge' => 'tested',
            'clearanceLevel' => 'tested'
        ], ['at' => 'annotationTypes']);
    }

    private function shouldAskForNormalizer($normalizers, $normalizer, $name)
    {
        $normalizers->getNormalizer($name)->willReturn($normalizer)->shouldBeCalled();
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
