<?php

namespace spec\Fesor\RAML\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeNormalizerSpec extends ObjectBehavior
{

    function it_uses_string_as_default_type()
    {
        $this->normalize([])->shouldContainSubset([
            'type' => 'string',
        ]);
    }

    function it_guest_type_as_array_it_items_present()
    {
        $this->normalize(['items' => 'string'])
            ->shouldContainSubset([
                'type' => 'array',
            ]);
    }

    function it_guest_type_as_objet_it_properties_present()
    {
        $this->normalize(['properties' => []])
            ->shouldContainSubset([
                'type' => 'object',
            ]);
    }

    function it_supports_type_expressions_for_array_declaration()
    {
        $this->normalize(['type' => 'string[]'])
            ->shouldContainSubset([
                'type' => 'array',
                'items' => ['type' => 'string']
            ]);
    }

    function it_supports_type_expressions_for_union_types()
    {
        $this->normalize(['type' => 'number | string'])
            ->shouldContainSubset([
                'oneOf' => [
                    ['type' => 'string'],
                    ['type' => 'number'],
                ]
            ]);
    }

    function it_supports_complex_type_expressions()
    {
        $this->normalize(['type' => 'boolean | (number | string)[]'])
            ->shouldContainSubset([
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
            ], ['at' => 'oneOf']);
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
        ])->shouldContainSubset([
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
        ], ['at' => 'properties']);
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
        ])->shouldContainSubset([
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
        ])->shouldContainSubset([
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
        ])->shouldContainSubset([
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
        ])->shouldContainSubset([
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
}
