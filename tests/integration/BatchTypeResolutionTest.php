<?php

use \Fesor\RAML\Type;

class BatchTypeResolutionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Type\BatchTypeResolver
     */
    private $batcnTypeResolver;

    /**
     * @var Type\TypeRegistry
     */
    private $typeRegistry;

    function setUp()
    {
        $this->batcnTypeResolver = new Type\BatchTypeResolver(
            new Type\TypeConstructor(
                new \Fesor\RAML\Normalizer\TypeNormalizer()
            ),
            $this->typeRegistry = new Type\TypeRegistry()
        );
    }

    function testBatchTypeResolution()
    {
        $this->batcnTypeResolver->resolveTypeMap([
            'CorporateEmail' => [
                'type' => 'Email',
            ],
            'Email' => [
                'type' => 'string',
                'description' => 'Type description'
            ],
        ]);

        $emailType = $this->typeRegistry->resolve('Email');
        $corporateEmailType = $this->typeRegistry->resolve('CorporateEmail');

        $this->assertEquals(
            $emailType->description(),
            $corporateEmailType->description(),
            'Child type should use parent\'s description if not set'
        );
    }

    function testCyclycTypeDependencies()
    {
        $this->expectException(\RuntimeException::class);
        $this->batcnTypeResolver->resolveTypeMap([
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