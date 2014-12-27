<?php

namespace SimpleThings\AppBundle\Badge;

use DavidBadura\MarkdownBuilder\MarkdownBuilder;
use SimpleThings\AppBundle\Entity\Project;
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
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param MergeRequest $mergeRequest
     * @return string
     */
    public function getMarkdownForMergeRequest(MergeRequest $mergeRequest)
    {
        return $this->generateMarkdownBadge(
            $this->router->generate('mergerequest_lastcommit', ['id' => $mergeRequest->getId()], Router::ABSOLUTE_URL),
            $this->router->generate('mergerequest_imagebadge', ['id' => $mergeRequest->getId()], Router::ABSOLUTE_URL)
        );
    }

    /**
     * @param Project $project
     * @return string
     */
    public function getMarkdownForProject(Project $project)
    {
        return $this->generateMarkdownBadge(
            $this->router->generate('project_lastcommit', ['id' => $project->getId()], Router::ABSOLUTE_URL),
            $this->router->generate('project_imagebadge', ['id' => $project->getId()], Router::ABSOLUTE_URL)
        );
    }

    /**
     * @param string $link
     * @param string $image
     * @return string
     */
    private function generateMarkdownBadge($link, $image)
    {
        $markdown = new MarkdownBuilder();

        return $markdown->inlineLink(
            $link,
            $markdown->inlineImg($image, 'SimpSpection Status')
        );
    }
}
