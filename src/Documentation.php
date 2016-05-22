<?php

namespace Fesor\RAML;

class Documentation
{
    private $title;
    private $conteint;

    /**
     * Documentation constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->conteint = $content;
    }

    public function title()
    {
        return $this->title;
    }

    public function content()
    {
        return $this->conteint;
    }
}