<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use SimpleThings\AppBundle\Badge\ScoreCalculator;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Repository\CommitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Framework\Route("/merge-request")
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

        if (! $commit) {
            throw $this->createNotFoundException();
        }

        return $this->redirect($this->generateUrl('commit_show', ['id' => $commit->getId()]));
    }

    /**
     * @Framework\Route("/{id}/badge", name="mergerequest_imagebadge")
     *
     * @param MergeRequest $mergeRequest
     *
     * @return Response
     */
    public function badgeAction(MergeRequest $mergeRequest)
    {
        /** @var ScoreCalculator $scoreCalculator */
        $scoreCalculator = $this->get('simple_things_app.badge.score_calculator');

        $score = $scoreCalculator->get($this->getCommitRepository()->findLastForMergeRequest($mergeRequest));

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

    /**
     * @return CommitRepository
     */
    protected function getCommitRepository()
    {
        return $this->get('simpspector.app.repository.commit');
    }
}
