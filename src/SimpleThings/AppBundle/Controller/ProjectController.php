<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use SimpleThings\AppBundle\Badge\ScoreCalculator;
use SimpleThings\AppBundle\Entity\Project;
use SimpleThings\AppBundle\Repository\CommitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Framework\Route("/projects")
 */
class ProjectController extends Controller
{
    /**
     * @Framework\Route("/", name="project_list")
     * @Framework\Template()
     *
     * @return array
     */
    public function listAction()
    {
        return ['projects' => $this->get('simpspector.app.repository.project')->findAll()];
    }

    /**
     * @Framework\Route("/{id}/show", name="project_show")
     * @Framework\Template()
     *
     * @param Project $project
     *
     * @return array
     */
    public function showAction(Project $project)
    {
        $mergeRequestRepository = $this->get('simpspector.app.repository.merge_request');
        $commitRepository       = $this->get('simpspector.app.repository.commit');

        $mergeRequests = $mergeRequestRepository->findBy(['project' => $project], [
            'status' => 'DESC',
            'id'     => 'DESC'
        ]);

        if (! ($masterCommit = $commitRepository->findLastSuccessInMaster($project))) {
            $masterCommit = $commitRepository->findLastInMaster($project);
        }

        $projectCommits = $commitRepository->findCommitsByProject($project);

        return [
            'project'         => $project,
            'merge_requests'  => $mergeRequests,
            'master_commit'   => $masterCommit,
            'project_commits' => $projectCommits
        ];
    }

    /**
     * @Framework\Route("/{id}/master", name="project_master")
     * @Framework\Template()
     *
     * @param Project $project
     *
     * @return array
     */
    public function masterAction(Project $project)
    {
        $commitRepository = $this->get('simpspector.app.repository.commit');
        $commits          = $commitRepository->findByMaster($project, 10);

        $markdown = $this->get('simple_things_app.badge_generator')->getMarkdownForProject($project);

        return [
            'project'  => $project,
            'commits'  => $commits,
            'markdown' => $markdown
        ];
    }

    /**
     * @Framework\Route("/{id}/last-commit", name="project_lastcommit")
     *
     * @param Project $project
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function lastCommitAction(Project $project)
    {
        /** @var CommitRepository */
        $repository = $this->get('simpspector.app.repository.commit');
        $commit     = $repository->findLastInMaster($project);

        if (! $commit) {
            throw new NotFoundHttpException();
        }

        return $this->redirect($this->generateUrl('commit_show', ['id' => $commit->getId()]));
    }

    /**
     * @Framework\Route("/{id}/badge", name="project_imagebadge")
     *
     * @param Project $project
     *
     * @return Response
     */
    public function badgeAction(Project $project)
    {
        /** @var ScoreCalculator $scoreCalculator */
        $scoreCalculator = $this->get('simple_things_app.badge.score_calculator');

        /** @var CommitRepository $commitRepository */
        $commitRepository = $this->get('simpspector.app.repository.commit');

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
