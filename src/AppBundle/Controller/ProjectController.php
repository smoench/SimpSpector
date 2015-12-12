<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use AppBundle\Entity\Project;
use AppBundle\Repository\CommitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Framework\Route("/projects")
 *
 * @author Tobias Olry <tobias.olry@gmail.com>
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

        $projectCommits = $commitRepository->findCommitsByProject($project);

        return [
            'project'         => $project,
            'merge_requests'  => $mergeRequests,
            'project_commits' => $projectCommits
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
}
