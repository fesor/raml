<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use Fesor\RAML\Normalizer\NormalizerRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MethodNormalizerSpec extends ObjectBehavior
{
    function let(NormalizerRegistry $normalizerRegistry, Normalizer $typeNormalizer, Normalizer $propertiesNormalizer)
    {
        $normalizerRegistry->getNormalizer('properties')->willReturn($propertiesNormalizer);
        $normalizerRegistry->getNormalizer('type')->willReturn($typeNormalizer);

        $this->beConstructedWith($normalizerRegistry);
    }

    function it_normalizes_headers(Normalizer $propertiesNormalizer)
    {
        $this->shouldNormalizesTypes($propertiesNormalizer);

        $this->normalize([
            'headers' => [
                'X-Authorization' => [
                    'pattern' => '^Bearer .+'
                ],
                'X-Other-Header?' => 'string',
            ]
        ]);
    }

    function it_normalizes_request_bodies(Normalizer $typeNormalizer)
    {
        $this->shouldNormalizesTypes($typeNormalizer);

        $this->normalize([
            'body' => [
                'application/json' => 'Post',
                'application/xml' => 'PostXml'
            ]
        ]);
    }

    function it_supports_in_place_type_declaration_as_body(Normalizer $typeNormalizer)
    {
        $this->shouldNormalizesTypes($typeNormalizer);

        $this->normalize([
            'body' => [
                'properties' => []
            ]
        ])->shouldBeArray([
            'normalized' => true
        ], ['at' => 'body']);
    }

    function it_supports_type_expressions_as_body_declaration(Normalizer $typeNormalizer)
    {
        $this->shouldNormalizesTypes($typeNormalizer);

        $this->normalize([
            'body' => 'BasicPost | ExtendedPost'
        ]);
    }

    function it_normalizes_responses()
    {
        $this->normalize([
            'responses' => [
                '200' => []
            ]
        ])->shouldBeArray([
            [
                'statusCode' => 200,
                'headers' => []
            ]
        ], ['at' => 'responses']);
    }

    function it_normalizes_response_headers(Normalizer $propertiesNormalizer)
    {
        $this->shouldNormalizesTypes($propertiesNormalizer);

        $this->normalize([
            'responses' => [
                '200' => [
                    'headers' => [
                        'X-Header?' => 'string'
                    ]
                ]
            ]
        ])->shouldBeArray(
            ['normalized' => true],
            ['at' => 'responses/0/headers']
        );
    }

    function it_normalizes_response_bodies(Normalizer $typeNormalizer)
    {
        $this->shouldNormalizesTypes($typeNormalizer);

        $this->normalize([
            'responses' => [
                '200' => [
                    'body' => 'Post'
                ]
            ]
        ])->shouldBeArray(
            ['normalized' => true],
            ['at' => 'responses/0/body']
        );
    }

    private function shouldNormalizesTypes($normalizer)
    {
        $normalizer->normalize(Argument::any())->willReturn(['normalized' => true])->shouldBeCalled();
    }
}
