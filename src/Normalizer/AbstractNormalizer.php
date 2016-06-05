<?php


namespace Fesor\RAML\Normalizer;

use Fesor\RAML\Builder;
use Fesor\RAML\RamlBuilderAware;

abstract class AbstractNormalizer implements Normalizer, RamlBuilderAware
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param Builder $builder
     */
    public function setRamlBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function priority()
    {
        return 50;
    }

    public function supportsDirection()
    {
        return self::DIRECTION_INWARD;
    }
}