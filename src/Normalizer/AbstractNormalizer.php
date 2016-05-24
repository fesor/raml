<?php


namespace Fesor\RAML\Normalizer;


abstract class AbstractNormalizer implements Normalizer
{
    public function priority()
    {
        return 50;
    }

    public function supportsDirection()
    {
        return self::DIRECTION_INWARD;
    }
}