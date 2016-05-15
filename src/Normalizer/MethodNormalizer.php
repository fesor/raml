<?php

namespace Fesor\RAML\Normalizer;

class MethodNormalizer implements Normalizer
{
    private $normalizerRegistry;

    /**
     * MethodNormalizer constructor.
     * @param NormalizerRegistry $normalizerRegistry
     */
    public function __construct(NormalizerRegistry $normalizerRegistry)
    {
        $this->normalizerRegistry = $normalizerRegistry;
    }

    public function normalize($value)
    {
        $value['headers'] = $this->normalizeHeaders(isset($value['headers']) ? $value['headers'] : []);
        if (isset($value['body'])) {
            $value['body'] = $this->normalizeBody($value['body']);
        }

        if (isset($value['responses'])) {
            $value['responses'] = $this->normalizeResponses($value['responses']);
        }

        return $value;
    }

    private function normalizeHeaders(array $headers)
    {
        return $this->normalizerRegistry
            ->getNormalizer('properties')
            ->normalize($headers);
    }

    private function normalizeBody($body, $checkMediaTypes = true)
    {
        if (!is_array($body)) {
            $body = ['type' => $body];
        }

        $mediaTypes = array_filter(array_keys($body), 'Fesor\\RAML\\isValidMediaType');
        if ($checkMediaTypes && count($mediaTypes) === count($body)) {
            $normalizedBody = [];
            foreach ($body as $mediaType => $bodyDeclaration) {
                $normalizedBodyDeclaration = $this->normalizeBody($bodyDeclaration);
                $normalizedBodyDeclaration['mediaType'] = $mediaType;
            }

            return $normalizedBody;
        }

        return $this->normalizerRegistry->getNormalizer('type')->normalize($body);
    }

    private function normalizeResponses(array $responses)
    {
        $normalizedResponses = [];
        foreach($responses as $statusCode => $responseDeclaration) {
            $response = $responseDeclaration;
            $response['headers'] = $this->normalizeHeaders(
                isset($responseDeclaration['headers']) ?
                    $responseDeclaration['headers'] : []
            );
            if (isset($responseDeclaration['body'])) {
                $response['body'] = $this->normalizeBody($responseDeclaration['body']);
            }
            $response['statusCode'] = $statusCode;

            $normalizedResponses[] = $response;
        }

        return $normalizedResponses;
    }
}
