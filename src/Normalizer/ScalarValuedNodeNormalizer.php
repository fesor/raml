<?php

namespace Fesor\RAML\Normalizer;

class ScalarValuedNodeNormalizer implements Normalizer
{
    private static $scalarValuedNodes = [
        'displayName', 'description', 'type', 'schema', 'default', 'example', 'usage',
        'repeat', 'required', 'content', 'strict', 'minLength', 'maxLength', 'uniqueItems',
        'minItems', 'maxItems', 'discriminator', 'minProperties', 'maxProperties',
        'discriminatorValue', 'pattern', 'format', 'minimum', 'maximum', 'multipleOf',
        'requestTokenUri', 'authorizationUri', 'tokenCredentialsUri', 'accessTokenUri',
        'title', 'version', 'baseUri', 'mediaType', 'extends',
    ];

    private $next;

    public function __construct(Normalizer $next)
    {
        $this->next = $next;
    }

    public function normalize($value)
    {
        foreach ($value as $key => $nodeValue) {
            if (in_array($key, self::$scalarValuedNodes)) {
                $value[$key] = $this->normalizeNodeValue($nodeValue);
            }
        }

        return $this->next->normalize($value);
    }

    private function normalizeNodeValue($value) {

        if (is_scalar($value)) {
            return [
                'value' => $value
            ];
        }

        // todo: handle errors?
        if (!isset($value['value'])) {
            $value['value'] = null;
        }

        return $value;
    }
}
