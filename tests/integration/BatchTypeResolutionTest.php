<?php

use \Fesor\RAML\Type;

class BatchTypeResolutionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Type\TypesMapFactory
     */
    private $typeMapFactory;

    function setUp()
    {
        $this->typeMapFactory = new Type\TypesMapFactory();
    }

    function testBatchTypeResolution()
    {
        $types = $this->typeMapFactory->create([
            'CorporateEmail' => [
                'type' => 'Email',
            ],
            'Email' => [
                'type' => 'string',
                'description' => 'Type description'
            ],
        ]);

        $emailType = $types['Email'];
        $corporateEmailType = $types['CorporateEmail'];

        $this->assertEquals(
            $emailType->description(),
            $corporateEmailType->description(),
            'Child type should use parent\'s description if not set'
        );
    }

    function testCyclycTypeDependencies()
    {
        $this->expectException(\RuntimeException::class);
        $this->typeMapFactory->create([
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