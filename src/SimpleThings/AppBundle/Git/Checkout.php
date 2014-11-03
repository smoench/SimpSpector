<?php

namespace SimpleThings\AppBundle\Git;

class Checkout
{
    public $url;
    public $revision;
    public $path;

    /**
     * @param string|null $url
     * @param string|null $revision
     * @param string|null $path
     */
    function __construct($url = null, $revision = null, $path = null)
    {
        $this->url = $url;
        $this->revision = $revision;
        $this->path = $path;
    }
}
