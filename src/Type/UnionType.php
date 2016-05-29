<?php


namespace Fesor\RAML\Type;


class UnionType extends Type
{
    private $types;

    /**
     * UnionType constructor.
     * @param Type[] $types
     */
    public function __construct($types)
    {
        $this->types = $types;
        parent::__construct([]);
    }

    /**
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }
}