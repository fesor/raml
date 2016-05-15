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

function withDefaultValues(array $defaults, array $data)
{
    return array_intersect_key(
        array_replace($defaults, $data),
        $defaults
    );
}

/**
 * Naive media type validator.
 *
 * Not sure does it covers all use cases
 * that covered in RFC 6838
 *
 * @param string $mediaType
 * @return boolean
 */
function isValidMediaType($mediaType)
{
    return !!preg_match('/^\w+\/[-+.\w]+$/', $mediaType);
}