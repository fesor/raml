<?php

namespace spec\Fesor\RAML\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeNormalizerSpec extends ObjectBehavior
{

    function it_uses_string_as_default_type()
    {
        $this->normalize([])->shouldReturnSubset([
            'type' => 'string',
        ]);
    }

    function it_guest_type_as_array_it_items_present()
    {
        $this->normalize(['items' => 'string'])
            ->shouldReturnSubset([
                'type' => 'array',
            ]);
    }

    function it_guest_type_as_objet_it_properties_present()
    {
        $this->normalize(['properties' => []])
            ->shouldReturnSubset([
                'type' => 'object',
            ]);
    }

    function it_supports_type_expressions_for_array_declaration()
    {
        $this->normalize(['type' => 'string[]'])
            ->shouldReturnSubset([
                'type' => 'array',
                'items' => ['type' => 'string']
            ]);
    }

    function it_supports_type_expressions_for_union_types()
    {
        $this->normalize(['type' => 'number | string'])
            ->shouldBeLike([
                'oneOf' => [
                    ['type' => 'string'],
                    ['type' => 'number'],
                ]
            ]);
    }

    function it_supports_complex_type_expressions()
    {
        $this->normalize(['type' => 'boolean | (number | string)[]'])
            ->shouldReturnSubset([
                'oneOf' => [
                    [
                        'type' => 'array',
                        'items' => [
                            'oneOf' => [
                                ['type' => 'string'],
                                ['type' => 'number'],
                            ]
                        ]
                    ],
                    ['type' => 'boolean'],
                ]
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

    function it_allows_to_use_type_expressions_in_properties()
    {
        $this->normalize([
            'properties' => [
                'foo?' => 'string | number'
            ]
        ])->shouldBeLike([
            'type' => 'object',
            'properties' => [
                'foo' => [
                    'oneOf' => [
                        ['type' => 'number'],
                        ['type' => 'string'],
                    ],
                    'required' => false
                ]
            ]
        ]);
    }

    function it_allows_to_use_type_expressions_in_array_items()
    {
        $this->normalize([
            'items' => 'string | number'
        ])->shouldBeLike([
            'type' => 'array',
            'items' => [
                'oneOf' => [
                    ['type' => 'number'],
                    ['type' => 'string'],
                ]
            ]
        ]);
    }

    function it_supports_property_patterns()
    {
        $this->normalize([
            'properties' => [
                'foo' => 'string',
                '/^notes/' => 'number',
                '//' => 'string'
            ]
        ])->shouldBeLike([
            'type' => 'object',
            'properties' => [
                'foo' => ['type' => 'string']
            ],
            'patternProperties' => [
                '^notes' => ['type' => 'number'],
                '' => ['type' => 'string']
            ]
        ]);
    }

    public function getMatchers()
    {
        return [
            'returnSubset' => function ($actual, $subset) {
                $actualSubset = array_intersect_key($actual, $subset);

                if ($actualSubset != $subset) {
                    echo json_encode($actualSubset, JSON_PRETTY_PRINT);
                    return false;
                }

                return true;
            }
        ];
    }
}
