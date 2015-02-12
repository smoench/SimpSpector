<?php

namespace SimpSpector\Provider;

use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Entity\Project;
use SimpSpector\Provider\Event\EventInterface;

/**
 *
 * @author David Badura <d.a.badura@gmail.com>
 * @author Simon Mönch <simonmoench@gmail.com>
 */
interface ProviderAdapterInterface
{
    /**
     * @param string $data
     *
     * @return EventInterface
     */
    public function handleEventData($data);

    /**
     * @param MergeRequest $mergeRequest
     *
     * @return string
     */
    public function addMergeRequestComment(MergeRequest $mergeRequest);

    /**
     * @param MergeRequest $mergeRequest
     *
     * @throws \Exception
     */
    public function updateMergeRequest(MergeRequest $mergeRequest);

    /**
     * @param string $projectId
     * @param string $branch
     *
     * @return string
     */
    public function getLastRevisionFromBranch($projectId, $branch);

    /**
     * @param string $remoteProjectId
     *
     * @return Project
     */
    public function createProject($remoteProjectId);

    /**
     * @return string
     */
    public function getName();
}