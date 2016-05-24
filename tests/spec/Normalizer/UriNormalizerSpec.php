<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Type\ObjectType;
use Fesor\RAML\Type\TypeConstructor;
use Fesor\RAML\Type\TypeResolver;
use Fesor\RAML\Uri;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UriNormalizerSpec extends ObjectBehavior
{
    private $resolver;
    private $typeConstructor;

    function let(TypeConstructor $typeConstructor, TypeResolver $resolver)
    {
        $this->resolver = $resolver;
        $this->typeConstructor = $typeConstructor;
        $this->setTypeConstructor($typeConstructor, $resolver);
    }

    function it_supports_nodes_only_containing_uri()
    {
        $this->supports([])->shouldReturn(true);
        $this->supports(['foo', 'resources'])->shouldReturn(false);
        $this->supports(['foo', 'resources', 0])->shouldReturn(false);
        $this->supports(['resources', 0])->shouldReturn(true);
        $this->supports(['resources', 0, 'methods'])->shouldReturn(false);
        $this->supports(['resources', 0, 'resources', 0])->shouldReturn(false);
    }

    function it_has_medium_priority()
    {
        $this->priority()->shouldReturn(50);
    }
    
    function it_converts_array_to_uri()
    {
        $this->shouldConstructProperties([]);

        $this->normalize([
            'uri' => '/users',
            'resources' => []
        ])->shouldBeLike([
            'uri' => new Uri('/users', new ObjectType([])),
            'resources' => []
        ]);
    }

    function it_constructs_uri_parameters_as_object_declaration()
    {
        $this->shouldConstructProperties(['id' => 'integer']);
        $this->normalize([
            'uri' => '/users/{id}',
            'uriParameters' => [
                'id' => 'integer'
            ],
            'resources' => []
        ])->shouldBeLike([
            'uri' => new Uri('/users/{id}', new ObjectType([])),
            'resources' => []
        ]);
    }

    function it_recursivly_normalizes_all_uris()
    {
        $parentUri = new Uri('/users', new ObjectType([]));
        $uri = new Uri('/{id}', new ObjectType([]), $parentUri);

        $this->shouldConstructProperties([]);
        $this->supports(['resources', 0]);
        $this->normalize([
            'uri' => '/users',
            'resources' => [
                [
                    'uri' => '/{id}',
                    'resources' => []
                ]
            ]
        ])->shouldBeLike([
            'uri' => $parentUri,
            'resources' => [
                [
                    'uri' => $uri,
                    'resources' => []
                ]
            ]
        ]);
    }

    function it_normalizes_base_uri()
    {
        $this->shouldConstructProperties([
            'host' => 'string',
            'version' => 'string'
        ]);
        
        $this->normalize([
            'baseUri' => 'http://{host}/api/{version}',
            'baseUriParameters' => [
                'host' => 'string'
            ]
        ])->shouldBeLike([
            'baseUri' => new Uri('http://{host}/api/{version}', new ObjectType([]))
        ]);
    }

    private function shouldConstructProperties(array $props)
    {
        $this->typeConstructor
            ->construct(['type' => 'object', 'properties' => $props], $this->resolver)
            ->willReturn(new ObjectType([]))
            ->shouldBeCalled();
    }
}
