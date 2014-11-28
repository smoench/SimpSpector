<?php

namespace SimpleThings\AppBundle\Badge;

use SimpleThings\AppBundle\Entity\MergeRequest;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @author David Badura <d.a.badura@gmail.com>
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
class Generator
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @param Router $router
     */
    function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param MergeRequest $mergeRequest
     * @return string
     */
    public function generate(MergeRequest $mergeRequest)
    {
        return vsprintf('[![Build Status](%(imageUrl)s)](%(linkUrl)s)', [
            'imageUrl' => $this->router->generate('image_badge', ['merge_request_id' => $mergeRequest->getId()]),
            'linkUrl'  => $this->router->generate('mergerequest_last_commit', ['id' => $mergeRequest->getId()]),
        ]);
    }
}
