<?php

namespace support\Fesor\RAML\PhpSpec;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Matcher\MatcherInterface;

class SubsetMatcher implements MatcherInterface
{
    public function supports($name, $subject, array $arguments)
    {
        return 'containSubset' === $name
        && 1 <= count($arguments)
        && (is_array($subject) && is_array($arguments[0]));
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        if (!$this->isContainsSubset($subject, $arguments)) {
            throw $this->getError(
                isset($arguments[1]['at']) ?
                    $arguments[1]['at'] : []
            );
        }

        return $subject;
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        if (!$this->isContainsSubset($subject, $arguments)) {
            throw $this->getError(
                isset($arguments[1]['at']) ?
                    $arguments[1]['at'] : [],
                $isNegative=true
            );
        }

        return $subject;
    }

    public function getPriority()
    {
        return 100;
    }

    private function isContainsSubset(array $subject, array $arguments)
    {
        $subset = $arguments[0];
        $options = isset($arguments[1]) ? $arguments[1] : [];

        if (isset($options['at'])) {
            $actual = $this->getAtPath($subject, $options['at']);
        } else {
            $actual = $subject;
        }

        $actual = $this->sortArrayKeys($this->deepIntersectKeys($actual, $subset));
        $expected = $this->sortArrayKeys($subset);

        return $actual == $expected;
    }

    private function sortArrayKeys($data)
    {
        if (!is_array($data) && !is_object($data)) {
            return $data;
        }

        $orderedData = $data;
        if (is_array($data)) {
            ksort($orderedData);
        }

        foreach ($orderedData as &$value) {
            $value = $this->sortArrayKeys($value);
        }

        return $orderedData;
    }

    private function getAtPath($data, $path)
    {
        $pathSegments = explode('/', trim($path, '/'));
        foreach ($pathSegments as $key) {

            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                throw new \RuntimeException(sprintf('Value doesn\'t have path "%s"', $path));
            }
        }

        return $data;
    }

    private function deepIntersectKeys(array $subject, array $expected)
    {
        $intersection = array_intersect_key($subject, $expected);

        foreach ($intersection as $key => &$value) {
            if (is_array($expected[$key])) {
                $value = $this->deepIntersectKeys($value, $expected[$key]);
            }
        }

        return $intersection;
    }

    private function getError($path, $isNegative=false)
    {
        $not = $isNegative ? ' not' : '';
        $withinPath = $path ? sprintf(' within path "%s"', $path) : '';
        return new FailureException(
            sprintf('Expected subject to%s include subset%s', $not, $withinPath)
        );
    }
}