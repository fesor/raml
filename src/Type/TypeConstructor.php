<?php

namespace Fesor\RAML\Type;

use Fesor\RAML\Normalizer\TypeNormalizer;

class TypeConstructor
{
    private $typeExpressionParser;

    private $requiredByDefault;

    private static $typeDeclarationClassMap = [
        'string' => StringType::class,
        'number' => NumberType::class,
        'boolean' => BooleanType::class,
        'null' => NullType::class,
        'array' => ArrayType::class,
        'object' => ObjectType::class
    ];

    /**
     * TypeConstructor constructor.
     * @param TypeNormalizer $typeExpressionParser
     * @param bool $requiredByDefault
     */
    public function __construct(TypeNormalizer $typeExpressionParser, $requiredByDefault = false)
    {
        $this->typeExpressionParser = $typeExpressionParser;
        $this->requiredByDefault = $requiredByDefault;
    }

    /**
     * @param $typeDeclaration
     * @param TypeResolver $typeResolver
     * @return Type
     */
    public function construct($typeDeclaration, TypeResolver $typeResolver)
    {
        $expandedTypeDeclaration = $typeDeclaration;
        if (is_string($typeDeclaration)) {
            $expandedTypeDeclaration = $this->typeExpressionParser->normalize($typeDeclaration);
        }

        if (!isset($expandedTypeDeclaration['type']) && isset($expandedTypeDeclaration['oneOf'])) {
            // todo: union types
            throw new \RuntimeException('Union types are not implemented');
        }

        if (!isset($expandedTypeDeclaration['type'])) {
            throw new \RuntimeException('Unable to register unknown type');
        }

        $type = $expandedTypeDeclaration['type'];
        $expandedTypeDeclaration = array_diff_key(
            $expandedTypeDeclaration,
            ['type' => null]
        );

        if ($type === 'array' && !empty($expandedTypeDeclaration['items'])) {
            $expandedTypeDeclaration['items'] = $this->construct($expandedTypeDeclaration['items'], $typeResolver);
        }

        $parent = null;
        if (isset(self::$typeDeclarationClassMap[$type])) {
            $className = self::$typeDeclarationClassMap[$type];
            return new $className($expandedTypeDeclaration);
        }
        $parent = $typeResolver->resolve($type);

        if (is_string($typeDeclaration)) {
            return $parent;
        }

        return $parent->extend($expandedTypeDeclaration);
    }
}
