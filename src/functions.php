<?php

namespace Fesor\RAML;

function onlyWithinKeys(array $data, array $keys)
{
    return array_intersect_key($data, array_flip($keys));
}

function excludingKeys(array $data, array $keys)
{
    return array_diff_key($data, array_flip($keys));
}