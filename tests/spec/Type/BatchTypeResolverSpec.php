<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\BatchTypeResolver;
use Fesor\RAML\Type\TypeConstructor;
use Fesor\RAML\Type\TypeRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BatchTypeResolverSpec extends ObjectBehavior
{
    function let(TypeConstructor $constructor, TypeRegistry $typeRegistry)
    {
        $this->beConstructedWith($constructor, $typeRegistry);
    }

    function it_allows_to_resolve_map_of_types(TypeConstructor $constructor, TypeRegistry $typeRegistry)
    {
        $this->shouldBeConstructed('foo', ['type' => 'string'], $constructor, $typeRegistry);
        $this->shouldBeConstructed('bar', ['type' => 'foo'], $constructor, $typeRegistry);
        $this->resolveTypeMap([
            'foo' => ['type' => 'string'],
            'bar' => ['type' => 'foo']
        ]);
    }

    private function shouldBeConstructed(
        $name, $declaration, TypeConstructor $constructor, TypeRegistry $typeRegistry
    )
    {
        $constructor->construct(
            $declaration,
            Argument::type(BatchTypeResolver::class)
        )->willReturnArgument(0)->shouldBeCalled();

        $typeRegistry->register($name, $declaration)->shouldBeCalled();
    }
}
