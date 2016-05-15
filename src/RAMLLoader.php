<?php

namespace Fesor\RAML;

interface RAMLLoader
{
    /**
     * @param string $url
     * @return RAML
     */
    public function load($url);
}