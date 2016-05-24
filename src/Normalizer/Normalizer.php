<?php

namespace Fesor\RAML\Normalizer;

interface Normalizer
{
    const DIRECTION_INWARD = 1;
    const DIRECTION_ANY = 0;
    const DIRECTION_OUTWARD = -1;

    /**
     * Normalize structure from one to another
     *
     * @param array $value
     * @return mixed
     */
    public function normalize(array $value);

    /**
     * Checks is normalizer supports node by given path
     *
     * @param string[] $path
     * @return boolean
     */
    public function supports(array $path);

    /**
     * Returns priority of normalizer to make it act in order
     *
     * @return integer
     */
    public function priority();

    /**
     * Returns supported direction of normalization process
     *
     * @return int
     */
    public function supportsDirection();
}