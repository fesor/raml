<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\RAMLLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImportNormalizerSpec extends ObjectBehavior
{
    function let(RAMLLoader $loader)
    {
        $this->beConstructedWith($loader);
    }

    function it_supports_import_tag(RAMLLoader $loader)
    {
        $loader->load('myTypes.raml')->willReturn('imported')->shouldBeCalled();

        $this
            ->normalize(['types' => '!include myTypes.raml'])
            ->shouldBeLike(['types' => 'imported']);
    }

    function xit_collects_all_imports_recursivly(RAMLLoader $loader)
    {
        $loader->load('a.raml')->willReturn('a')->shouldBeCalled();
        $loader->load('b.raml')->willReturn('b')->shouldBeCalled();

        $this->normalize([
            'foo' => ['a' => '!include a.raml'],
            'bar' => ['b' => '!include b.raml']
        ])->shouldBeLike([
            'foo' => ['a' => 'a'],
            'bar' => ['b' => 'b']
        ]);
    }
}
