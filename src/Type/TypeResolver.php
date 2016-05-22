<?php

namespace Fesor\RAML\Type;

interface TypeResolver
{
    public function resolve($typeName);
}
