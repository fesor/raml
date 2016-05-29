<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Builder;
use Fesor\RAML\Normalizer\Normalizer;
use Fesor\RAML\Type\StringType;
use Fesor\RAML\Type\Type;
use Fesor\RAML\Type\TypeRegistry;
use Fesor\RAML\Type\TypesMapFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TypesNormalizerSpec extends ObjectBehavior
{
    function let(TypesMapFactory $typeRegistryFactory, Builder $builder)
    {
        $this->beConstructedWith($typeRegistryFactory);
        $this->setRamlBuilder($builder);
    }

    function it_has_high_priority()
    {
        $this->priority()->shouldReturn(20);
    }

    function it_supports_only_inward_processing()
    {
        $this->supportsDirection(Normalizer::DIRECTION_INWARD);
    }

    function it_normalize_only_root_node(TypesMapFactory $typeRegistryFactory)
    {
        $typeRegistryFactory->create()->shouldNotBeCalled();
        $this->normalize([], ['foo'])->shouldReturn([]);
    }

    function it_normalizes_type_map(TypesMapFactory $typeRegistryFactory, Builder $builder)
    {
        $type = Type::named('test', new StringType([]));
        $typeRegistryFactory
            ->create(['test' => 'string'])
            ->willReturn(['test' => $type])
            ->shouldBeCalled();
        $builder->registerType($type)->shouldBeCalled();

        $this
            ->normalize([
                'types' => [
                    'test' => 'string'
                ]
            ], [])
            ->shouldReturn([]);
    }
}
