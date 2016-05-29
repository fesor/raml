<?php

namespace Fesor\RAML\Type;

use Fesor\RAML\Normalizer\TypeNormalizer;
use function Fesor\RAML\onlyWithinKeys;

class TypeConstructor
{
    private $typeResolver;

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
     * @param TypeResolver $typeResolver
     */
    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    public function createType($typeDeclaration, $name = null)
    {
        if (is_string($typeDeclaration)) {
            return Type::named($name, $this->parseExpression($typeDeclaration));
        }

        $typeDeclaration = $this->normalizeTypeDeclaration($typeDeclaration);
        $type = $typeDeclaration['type'];
        unset($typeDeclaration['type']);

        if (is_array($type)) {
            // todo: handle multiple inheritance
            return null;
        }

        switch ($type) {
            case 'string':
            case 'number':
            case 'null':
            case 'boolean':
                $className = self::$typeDeclarationClassMap[$type];

                return Type::named($name, new $className($typeDeclaration));
            case 'object':
                return $this->createObject($typeDeclaration);
            case 'array':
                $typeDeclaration['items'] = isset($typeDeclaration['items']) ?
                    $this->createType($typeDeclaration['items']) : Type::any();

                return new ArrayType($typeDeclaration);
            default:
                if (preg_match('/^[\w ]+$/', $type)) {
                    return Type::named($name, $this->typeResolver->resolve($type)->extend($typeDeclaration));
                }

                $baseType = $this->createType($type);
                if (empty($typeDeclaration)) {
                    return $baseType;
                }

                return Type::named($name, $baseType->extend($typeDeclaration));
        }
    }

    private function createObject(array $typeDeclaration)
    {
        $propertyDeclarations = isset($typeDeclaration['properties']) ?
            $typeDeclaration['properties'] : [];
        unset($typeDeclaration['properties']);

        $typeDeclaration['patternProperties'] = array_map(function ($propertyDeclaration) {
            return $this->createType($propertyDeclaration);
        }, $this->filterPatternProperties($propertyDeclarations));
        $propertyDeclarations = $this->filterPatternProperties($propertyDeclarations, true);

        $properties = [];
        foreach ($propertyDeclarations as $property => $propertyDeclaration) {
            if (
                mb_substr($property, -1) === '?' && (
                    !is_array($propertyDeclaration)
                    || !array_key_exists('required', $propertyDeclaration)
                )
            ) {
                $property = mb_substr($property, 0, -1);
                if (!is_array($propertyDeclaration)) {
                    $propertyDeclaration = ['type' => $propertyDeclaration];
                }
                $propertyDeclaration['required'] = false;
            }

            $properties[$property] = $this->createType($propertyDeclaration);
        }

        return new ObjectType($typeDeclaration, $properties);
    }

    private function filterPatternProperties(array $properties, $excludePatternProperties = false)
    {
        $keys = array_filter(array_keys($properties), function ($key) use ($excludePatternProperties) {
            return $excludePatternProperties ^ (!!preg_match('/^\/.*\/$/', $key));
        });

        return onlyWithinKeys($properties, $keys);
    }

    /**
     * @param string $type
     * @return Type
     */
    private function parseExpression($type)
    {
        $rpn = $this->rpn($type);

        $stack = [];
        foreach ($rpn as $token) {
            switch ($token) {
                case '|':
                    $operands = [array_pop($stack), array_pop($stack)];
                    if ($operands[0] instanceof UnionType) {
                        $operands = array_merge(
                            $operands[0]->getTypes(),
                            $operands[1]
                        );
                    }

                    $stack[] = new UnionType($operands);
                    break;

                case '?':
                    $stack[] = new UnionType([
                        new NullType(),
                        array_pop($stack)
                    ]);
                    break;
                case '[]':
                    $stack[] = new ArrayType([
                        'items' => array_pop($stack)
                    ]);
                    break;
                default:
                    $stack[] = $this->createType([
                        'type' => $token
                    ]);
                    break;
            }
        }

        return $stack[0];
    }

    /**
     * Parses type expression to RPN
     *
     * @param string $str
     * @return array
     */
    private function rpn($str)
    {
        preg_match_all('/((\[\]){1}|[\(\)\|\?]|[^\(\)\|\?\[\]\s]+?)/U', $str, $matches);
        $tokens = $matches[0];
        $stack = [];
        $out = [];
        $operators = array_flip(['|', '[]', '?']);

        foreach ($tokens as $token) {
            switch ($token) {
                case '|':
                case '?':
                case '[]':
                    while (!empty($stack) && array_key_exists(end($stack), $operators)) {
                        if ($operators[$token] < $operators[end($stack)]) {
                            $out[] = array_pop($stack);
                            continue;
                        }
                        break;
                    }
                    $stack[] = $token;
                    break;
                case '(':
                    $stack[] = $token;
                    break;
                case ')':
                    while (!empty($stack) && end($stack) !== '(') {
                        $out[] = array_pop($stack);
                    }
                    array_pop($stack);
                    break;
                default:
                    $out[] = $token;
                    break;
            }
        }

        return array_merge($out, array_reverse($stack));
    }

    private function normalizeTypeDeclaration(array $typeDeclaration)
    {
        if (!isset($typeDeclaration['type']) && isset($typeDeclaration['properties'])) {
            $typeDeclaration['type'] = 'object';
        }

        return array_replace([
            'type' => 'string'
        ], $typeDeclaration);
    }
}
