<?php

namespace Fesor\RAML\Type;

interface TypeResolver
{
    /**
     * @param $typeName
     * @return Type
     */
    public function resolve($typeName);

    /**
     * @param Type $typeDefinition
     * @return void
     */
    public function register(Type $typeDefinition);
}
