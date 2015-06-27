<?php

namespace AppBundle\Provider;

use AppBundle\Provider\Struct\MergeRequest;
use AppBundle\Provider\Struct\Project;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @param int $projectId
     * @return Project
     */
    public function getProjectInformation($projectId);

    /**
     * @param int $projectId
     * @param int $mergeRequestId
     * @return MergeRequest
     */
    public function getMergeRequestInformation($projectId, $mergeRequestId);

    /**
     * @param int $projectId
     * @param int $mergeRequestId
     * @param string $comment
     * @return
     */
    public function addMergeRequestComment($projectId, $mergeRequestId, $comment);
}