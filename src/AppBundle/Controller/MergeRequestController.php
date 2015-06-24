<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Repository\CommitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Framework\Route("/merge-request")
 *
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class MergeRequestController extends Controller
{
    /**
     * @Framework\Route("/{id}/show", name="mergerequest_show")
     * @Framework\Template()
     *
     * @param MergeRequest $mergeRequest
     *
     * @return Response
     */
    public function showAction(MergeRequest $mergeRequest)
    {
        $commits = $this->getCommitRepository()->findBy(['mergeRequest' => $mergeRequest], ['id' => 'DESC']);

        return ['merge_request' => $mergeRequest, 'commits' => $commits];
    }

    /**
     * @Framework\Route("/{id}/last-commit", name="mergerequest_lastcommit")
     *
     * @param MergeRequest $mergeRequest
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function lastCommitAction(MergeRequest $mergeRequest)
    {
        $commit = $this->getCommitRepository()->findLastForMergeRequest($mergeRequest);

        if (!$commit) {
            throw $this->createNotFoundException();
        }

        return $this->redirect($this->generateUrl('commit_show', ['id' => $commit->getId()]));
    }

    /**
     * @return CommitRepository
     */
    protected function getCommitRepository()
    {
        return $this->get('simpspector.app.repository.commit');
    }
}
