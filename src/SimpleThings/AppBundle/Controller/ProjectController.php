<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Badge\ScoreCalculator;
use SimpleThings\AppBundle\Entity\Project;
use SimpleThings\AppBundle\Repository\CommitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/{id}/show", name="project_show")
     */
    public function showAction(Project $project)
    {
        $mergeRequestRepository = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\MergeRequest');
        $commitRepository       = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Commit');

        $mergeRequests = $mergeRequestRepository->findBy(['project' => $project], ['id' => 'DESC']);
        $masterCommits = $commitRepository->findByMaster(3);

        return $this->render(
            "SimpleThingsAppBundle:Project:show.html.twig",
            [
                'project'        => $project,
                'merge_requests' => $mergeRequests,
                'masterCommits'  => $masterCommits
            ]
        );
    }

    /**
     * @Route("/{id}/master", name="project_master")
     */
    public function masterAction(Project $project)
    {
        $commitRepository = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Commit');

        $commits = $commitRepository->findByMaster();

        return $this->render(
            "SimpleThingsAppBundle:Project:master.html.twig",
            [
                'project' => $project,
                'commits' => $commits
            ]
        );
    }

    /**
     * @Route("/{id}/badge", name="project_imagebadge")
     *
     * @param Project $project
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function badgeAction(Project $project)
    {
        /** @var ScoreCalculator $scoreCalculator */
        $scoreCalculator = $this->get('simple_things_app.badge.score_calculator');

        /** @var CommitRepository $commitRepository */
        $commitRepository = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\Commit');

        $score = $scoreCalculator->get($commitRepository->findLastInMaster());

        $response = new Response($this->renderView("SimpleThingsAppBundle:Image:show.xml.twig", [
            'score' => $score->number,
            'color' => $score->color,
        ]), 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="status.svg"'
        ]);
        $response->setMaxAge(0);
        $response->setExpires(new \DateTime('-1 hour'));

        return $response;
    }
}
