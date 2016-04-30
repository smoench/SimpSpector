<?php

namespace AppBundle\Provider;

use AppBundle\Entity\MergeRequest;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @param string $url
     * @param string $token
     */
    public function __construct($url, $token);

    /**
     * @param MergeRequest $mergeRequest
     * @param string $comment
     */
    public function addMergeRequestComment(MergeRequest $mergeRequest, $comment);
}