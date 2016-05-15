<?php

namespace spec\Fesor\RAML\Type;

use Fesor\RAML\Type\TypeRegistry;

trait TypeSpecTrait
{
    protected $registry;

    public function let()
    {
        $this->registry = new TypeRegistry();
    }

    protected function fromArray($typeDefinition)
    {
        $this->beConstructedWith('example', array_replace(
            ['type' => 'string'],
            $typeDefinition
        ));
    }
}