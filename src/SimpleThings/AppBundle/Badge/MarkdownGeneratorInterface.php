<?php

namespace SimpleThings\AppBundle\Badge;

use DavidBadura\MarkdownBuilder\MarkdownBuilder;
use SimpleThings\AppBundle\Entity\Project;
use SimpleThings\AppBundle\Entity\MergeRequest;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
interface MarkdownGeneratorInterface
{
    /**
     * @param MergeRequest $mergeRequest
     * @return string
     */
    public function generateForMergeRequest(MergeRequest $mergeRequest);

    /**
     * @param Project $project
     * @return string
     */
    public function generateForProject(Project $project);
}
