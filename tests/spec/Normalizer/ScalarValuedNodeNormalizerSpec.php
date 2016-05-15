<?php

namespace spec\Fesor\RAML\Normalizer;

use Fesor\RAML\Normalizer\Normalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ScalarValuedNodeNormalizerSpec extends ObjectBehavior
{
    function let(Normalizer $next)
    {
        $this->beConstructedWith($next);
        $next->normalize(Argument::any())->willReturnArgument(0);
    }

    function it_normalizes_scalar_valued_nodes()
    {
        $scalarValuedNodes = [
            'displayName',
            'description',
            'type',
            'schema',
            'default',
            'example',
            'usage',
            'repeat',
            'required',
            'content',
            'strict',
            'minLength',
            'maxLength',
            'uniqueItems',
            'minItems',
            'maxItems',
            'discriminator',
            'minProperties',
            'maxProperties',
            'discriminatorValue',
            'pattern',
            'format',
            'minimum',
            'maximum',
            'multipleOf',
            'requestTokenUri',
            'authorizationUri',
            'tokenCredentialsUri',
            'accessTokenUri',
            'title',
            'version',
            'baseUri',
            'mediaType',
            'extends',
        ];

        foreach ($scalarValuedNodes as $scalarValuedNode) {
            $this->normalize([
                $scalarValuedNode => 'scalar value'
            ])->shouldBeLike([
                $scalarValuedNode => [
                    'value' => 'scalar value'
                ]
            ]);
        }
    }

    function it_normalize_object_value()
    {
        $this->normalize([
            'title' => [
                '(annotation)' => 'value'
            ]
        ])->shouldContainSubset([
            'value' => null,
            '(annotation)' => 'value'
        ], ['at' => 'title']);
    }
}
