<?php

namespace Fesor\RAML;

interface RAMLLoader
{
    /**
     * @param string $url
     * @return array
     */
    public function load($url);
}