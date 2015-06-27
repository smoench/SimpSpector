<?php

namespace AppBundle\Provider\Struct;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Project
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $repositoryUrl;

    /**
     * @var string
     */
    public $webUrl;
}