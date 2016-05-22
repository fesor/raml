<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Normalizer\TypeNormalizer;
use Fesor\RAML\Type\ArrayType;
use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\Type;
use Fesor\RAML\Type\TypeResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypeConstructorSpec extends ObjectBehavior
{
    function let(TypeNormalizer $normalizer)
    {
        $this->beConstructedWith($normalizer);
    }

    function it_constructs_scalar_types_from_string(TypeNormalizer $normalizer, TypeResolver $resolver)
    {
        $normalizer->normalize('string')->willReturn(['type' => 'string'])->shouldBeCalled();
        $this->construct('string', $resolver)->shouldReturnAnInstanceOf(Type::class);
    }

    function it_constructs_user_defined_types_from_string(TypeNormalizer $normalizer, TypeResolver $resolver)
    {
        $normalizer->normalize('User')->willReturn(['type' => 'User'])->shouldBeCalled();
        $resolver->resolve('User')->willReturn('User')->shouldBeCalled();

        $this->construct('User', $resolver)->shouldReturn('User');
    }

    function it_constructs_array_from_type_expression(TypeNormalizer $normalizer, TypeResolver $resolver)
    {
        $normalizer->normalize('User[]')->willReturn(['type' => 'array', 'items' => 'User'])->shouldBeCalled();
        $normalizer->normalize('User')->willReturn(['type' => 'User'])->shouldBeCalled();
        $resolver->resolve('User')->willReturn('User')->shouldBeCalled();

        $this->construct('User[]', $resolver)->shouldReturnAnInstanceOf(ArrayType::class);
    }

    function it_constructs_objects(TypeNormalizer $normalizer, TypeResolver $resolver)
    {
        $normalizer->normalize(['type' => 'object'])->willReturn([
            'type' => 'object',
            'properties' => [
                'foo' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ]);

        $this->construct(['type' => 'object'], $resolver)->shouldReturnAnInstanceOf(ObjectType::class);
    }

}
