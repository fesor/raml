<?php


namespace spec\Fesor\RAML\Type;

use PhpSpec\ObjectBehavior;

abstract class TypeObjectBehaviour extends ObjectBehavior
{
    function it_returns_description()
    {
        $this->withFacets(['description' => 'test description']);

        $this->description()->shouldReturn('test description');
    }

    function it_returns_display_name()
    {
        $this->withFacets(['displayName' => 'example type']);

        $this->displayName()->shouldReturn('example type');
    }

    protected function withFacets(array $facets = [])
    {
        $this->beConstructedWith($facets);
    }

    public function getMatchers()
    {
        return [
            'returnExtendedType' => function ($subject, $fn) {
                return $fn($subject);
            }
        ];
    }
}