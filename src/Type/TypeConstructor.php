<?php

namespace Fesor\RAML\Type;

use Fesor\RAML\Normalizer\TypeNormalizer;

class TypeConstructor
{
    private $typeExpressionParser;

    private static $typeDeclarationClassMap = [
        'string' => StringType::class,
        'number' => NumberType::class,
    ];

    /**
     * TypeConstructor constructor.
     * @param $typeExpressionParser
     */
    public function __construct(TypeNormalizer $typeExpressionParser)
    {
        $this->typeExpressionParser = $typeExpressionParser;
    }

    public function construct($typeDeclaration, TypeResolver $typeResolver)
    {
        if (is_string($typeDeclaration)) {

        }

        if (!isset($typeDeclaration['type']) && isset($typeDeclaration['oneOf'])) {
            // todo: union types
            throw new \RuntimeException('Union types are not implemented');
        }

        if (!isset($typeDeclaration['type'])) {
            throw new \RuntimeException('Unable to register unknown type');
        }

        $type = $typeDeclaration['type'];
        $typeDeclaration = array_diff_key(
            $typeDeclaration,
            ['type' => null]
        );

        $parent = null;
        if (isset(self::$typeDeclarationClassMap[$type])) {
            $className = self::$typeDeclarationClassMap[$type];
            return new $className($typeDeclaration);
        }

        $parent = $typeResolver->resolve($type);

        return $parent->extend($typeDeclaration);
    }
    
    
}
