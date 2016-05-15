<?php

namespace support\Fesor\RAML\PhpSpec;

use PhpSpec\Extension\ExtensionInterface;
use \PhpSpec\ServiceContainer;

class Extension implements ExtensionInterface
{
    public function load(ServiceContainer $container)
    {
        $container->set('matchers.includes', function (ServiceContainer $c) {
            return new SubsetMatcher();
        });
        $container->set('matchers.subset_equals', function (ServiceContainer $c) {
            return new DeepEqualsMatcher();
        });
    }
}