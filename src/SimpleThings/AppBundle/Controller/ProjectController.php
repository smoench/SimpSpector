<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use SimpleThings\AppBundle\Badge\ScoreCalculator;
use SimpleThings\AppBundle\Entity\Project;
use SimpleThings\AppBundle\Repository\CommitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/project")
 */
class ProjectController extends Controller
{

    /**
     * @Route("s", name="project_list")
     */
    public function listAction()
    {
        $projectRepository = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Project');
        $projects          = $projectRepository->findBy([], ['name' => 'ASC']);

        return $this->render(
            "SimpleThingsAppBundle:Project:list.html.twig",
            [
                'projects' => $projects,
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="project_show")
     *
     * @param Project $project
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Project $project)
    {
        $mergeRequestRepository = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\MergeRequest');
        $commitRepository       = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Commit');

        $mergeRequests = $mergeRequestRepository->findBy(['project' => $project], [
            'status' => 'DESC',
            'id'     => 'DESC'
        ]);

        if (!$masterCommit = $commitRepository->findLastSuccessInMaster($project)) {
            $masterCommit = $commitRepository->findLastInMaster($project);
        }

        $projectCommits = $commitRepository->findCommitsByProject($project);

        return $this->render(
            "SimpleThingsAppBundle:Project:show.html.twig",
            [
                'project'         => $project,
                'merge_requests'  => $mergeRequests,
                'master_commit'   => $masterCommit,
                'project_commits' => $projectCommits
            ]
        );
    }

    /**
     * @Route("/{id}/master", name="project_master")
     *
     * @param Project $project
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function masterAction(Project $project)
    {
        $commitRepository = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Commit');
        $commits          = $commitRepository->findByMaster($project, 10);

        $markdown = $this->get('simple_things_app.badge_generator')->getMarkdownForProject($project);

        return $this->render(
            "SimpleThingsAppBundle:Project:master.html.twig",
            [
                'project'  => $project,
                'commits'  => $commits,
                'markdown' => $markdown
            ]
        );
    }

    /**
     * @Route("/{id}/last-commit", name="project_lastcommit")
     */
    public function lastCommitAction(Project $project)
    {
        /** @var CommitRepository */
        $repository = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\Commit');
        $commit     = $repository->findLastInMaster($project);

        if (!$commit) {
            throw new NotFoundHttpException();
        }

        return $this->redirect($this->generateUrl('commit_show', ['id' => $commit->getId()]));
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

        $score = $scoreCalculator->get($commitRepository->findLastInMaster($project));

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
