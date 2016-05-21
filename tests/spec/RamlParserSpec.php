<?php

namespace spec\Fesor\RAML;

use Fesor\RAML\Type\TypeRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Yaml\Parser;

class RamlParserSpec extends ObjectBehavior
{
    private $yamlParserMock;

    function let(Parser $parser, TypeRegistry $typeRegistry)
    {
        $this->yamlParserMock = $parser;
        $this->beConstructedWith($parser, $typeRegistry);
    }

    function it_verifies_raml_metadata()
    {
        $this->yaml();
        $this->shouldNotThrow()->duringParse('#%RAML 1.0');
        $this->shouldThrow()->duringParse('#%RAML 0.8');
        $this->shouldThrow()->duringParse('#%RAML 1.1');
        $this->shouldNotThrow()->duringParse('#%RAML 1.0 DocumentationItem', 'DocumentationItem');
        $this->shouldThrow()->duringParse('#%RAML 1.0 DocumentationItem', 'Library');
        $this->shouldThrow()->duringParse('#%RAML 1.0 FooBar');
        $this->shouldNotThrow()->duringParse("\n#%RAML 1.0 FooBar");
    }

    private function yaml(array $raml = [])
    {
        $this->yamlParserMock->parse(Argument::any())->willReturn(
            array_replace(['title' => 'My API'], $raml)
        )->shouldBeCalled();
    }
}
