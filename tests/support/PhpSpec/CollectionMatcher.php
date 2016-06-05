<?php

namespace support\Fesor\RAML\PhpSpec;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Matcher\MatcherInterface;

class CollectionMatcher implements MatcherInterface
{
    public function supports($name, $subject, array $arguments)
    {
         return ('returnCollectionOfType' === $name && 1 === count($arguments))
             || ('containItemsWhich' === $name && 1 === count($arguments) && is_callable($arguments[0]));
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        if (!$this->containsOnlyItemsOfType($subject, $arguments[0])) {
            throw new FailureException($this->getPositiveError($name, $arguments));
        }
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        if ($this->containsOnlyItemsOfType($subject, $arguments[0])) {
            throw new FailureException($this->getNegativeError($name, $arguments));
        }
    }

    public function getPriority()
    {
        return 50;
    }

    private function getMatchingFunction($name)
    {
        return [$this, [
            'returnCollectionOfType' => 'containsOnlyItemsOfType',
            'containItemsWhich' => 'containsItemsWhich'
        ][$name]];
    }

    private function getPositiveError($name, array $arguments)
    {
        return [
            'returnCollectionOfType' => sprintf(
                'Collection should contain only elements of type "%s%', $arguments[0]
            ),
            'containItemsWhich' => sprintf(
                'Collection should contain only elements of type "%s%', $arguments[0]
            )
        ][$name];
    }

    private function getNegativeError($name, array $arguments)
    {
        return [
            'returnCollectionOfType' => sprintf(
                'Collection should not contain only elements of type "%s%', $arguments[0]
            ),
            'containItemsWhich' => sprintf(
                'Collection should not contain only elements of type "%s%', $arguments[0]
            )
        ][$name];
    }

    private function containsItemsWhich($subject, callable $matcher)
    {
        foreach ($subject as $item)
        {
            if (!$matcher($item)) {
                return false;
            }
        }

        return !empty($subject);
    }

    private function containsOnlyItemsOfType($subject, $type)
    {
        foreach ($subject as $item)
        {
            if (!is_a($item, $type)) {
                return false;
            }
        }

        return !empty($subject);
    }
}