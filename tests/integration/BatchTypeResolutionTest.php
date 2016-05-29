<?php

use \Fesor\RAML\Type;

class BatchTypeResolutionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Type\TypeRegistryFactory
     */
    private $typeRegistryFactory;

    /**
     * @var Type\TypeRegistry
     */
    private $typeRegistry;

    function setUp()
    {
        $this->typeRegistryFactory = new Type\TypeRegistryFactory();
    }

    function testBatchTypeResolution()
    {
        $registry = $this->typeRegistryFactory->create([
            'CorporateEmail' => [
                'type' => 'Email',
            ],
            'Email' => [
                'type' => 'string',
                'description' => 'Type description'
            ],
        ]);

        $emailType = $registry->resolve('Email');
        $corporateEmailType = $registry->resolve('CorporateEmail');

        $this->assertEquals(
            $emailType->description(),
            $corporateEmailType->description(),
            'Child type should use parent\'s description if not set'
        );
    }

    function testCyclycTypeDependencies()
    {
        $this->expectException(\RuntimeException::class);
        $this->typeRegistryFactory->create([
            'Cycle1' => [
                'type' => 'Middle'
            ],
            'Middle' => [
                'type' => 'Cycle2'
            ],
            'Cycle2' => [
                'type' => 'Cycle1',
            ],
        ]);
    }
}