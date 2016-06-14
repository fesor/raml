<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\RAMLLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ModulizationNormalizerSpec extends ObjectBehavior
{
    function let(RAMLLoader $loader)
    {
        $this->beConstructedWith($loader);
    }

    function it_should_import_values_recursivly(RAMLLoader $loader)
    {
        $loader->load('first.raml')->willReturn(['first' => 'value'])->shouldBeCalled();
        $loader->load('second.raml')->willReturn(['second' => 'value'])->shouldBeCalled();

        $this->normalize([
            'foo' => '!include first.raml',
            'bar' => [
                'included' => '!include second.raml'
            ],
            'buz' => [
                'non_import' => true
            ]
        ], [])
            ->shouldBeLike([
            'foo' => ['first' => 'value'],
            'bar' => [
                'included' => ['second' => 'value']
            ],
            'buz' => [
                'non_import' => true
            ]
        ]);
    }

}
