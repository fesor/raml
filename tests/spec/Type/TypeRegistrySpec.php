<?php

namespace spec\Fesor\RAML\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeRegistrySpec extends ObjectBehavior
{
    function it_can_register_type_declaration()
    {
        $this->shouldNotThrow()->duringRegister('name', [
            'type' => 'string'
        ]);
    }

    function it_by_default_contain_integer_type()
    {
        $this->shouldNotThrow()->duringResolve('integer');
    }

    function it_throws_exception_if_type_is_not_registered()
    {
        $this->shouldThrow()->duringResolve('not-registered-type');
    }

    function it_can_register_type_definition_of_string()
    {
        $this->shouldNotThrow()->duringRegister('user-defined', [
            'type' => 'string'
        ]);
    }

    function it_can_register_type_definition_of_number()
    {
        $this->shouldNotThrow()->duringRegister('user-defined', [
            'type' => 'number'
        ]);
    }

    function it_can_register_type_definition_of_boolean()
    {
        $this->shouldNotThrow()->duringRegister('user-defined', [
            'type' => 'boolean'
        ]);
    }

    function it_can_register_type_definition_of_null()
    {
        $this->shouldNotThrow()->duringRegister('user-defined', [
            'type' => 'null'
        ]);
    }

    function it_registers_user_defined_objects()
    {
        $this->shouldNotThrow()->duringRegister('user-defined', [
            'type' => 'object',
            'properties' => [
                'str' => [
                    'type' => 'string'
                ],
                'obj' => [
                    'type' => 'object',
                    'properties'
                ]
            ]
        ]);
    }
}
