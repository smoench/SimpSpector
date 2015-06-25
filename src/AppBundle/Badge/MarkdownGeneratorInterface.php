<?php

namespace AppBundle\Badge;

use DavidBadura\MarkdownBuilder\MarkdownBuilder;
use AppBundle\Entity\Project;
use AppBundle\Entity\MergeRequest;
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
