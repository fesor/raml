<?php

namespace spec\Fesor\RAML\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeNormalizerSpec extends ObjectBehavior
{
    function it_uses_string_type_by_default()
    {
        $this->normalize([])->shouldReturnSubset([
            'type' => 'string'
        ]);
    }

    function it_uses_object_type_if_properties_defined()
    {
        $this->normalize([
            'properties' => []
        ])->shouldReturnSubset([
            'type' => 'object'
        ]);
    }

    function it_uses_array_type_if_items_defined()
    {
        $this->normalize([
            'items' => []
        ])->shouldReturnSubset([
            'type' => 'array'
        ]);
    }

    function it_allows_short_array_declarations()
    {
        $this->normalize(['type' => 'Email[]'])->shouldReturnSubset([
            'type' => 'array',
            'items' => 'Email'
        ]);
    }

    function it_normalizes_object_properties_with_question_mark()
    {
        $this->normalize([
            'properties' => [
                'foo?' => 'string',
                'bar??' => 'string',
                'buz?' => [
                    'required' => true
                ]
            ]
        ])->shouldReturnSubset([
            'properties' => [
                'foo' => [
                    'type' => 'string',
                    'required' => false
                ],
                'bar?' => [
                    'type' => 'string',
                    'required' => false
                ],
                'buz?' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ]);
    }

    function it_allow_inplace_type_declaration()
    {
        $this->normalize([
            'properties' => [
                'foo' => [
                    'properties' => [
                        'bar?' => 'string'
                    ]
                ]
            ]
        ])->shouldReturnSubset([
            'type' => 'object',
            'properties' => [
                'foo' => [
                    'type' => 'object',
                    'properties' => [
                        'bar' => [
                            'type' => 'string',
                            'required' => false
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function getMatchers()
    {
        return [
            'returnSubset' => function ($actual, $subset) {
                $actualSubset = array_intersect_key($actual, $subset);

                return $actualSubset == $subset;
            },
            'containFacets' => function ($actual, $expectedFacets) {
                return $actual['facets'] == $expectedFacets;
            }
        ];
    }

}
