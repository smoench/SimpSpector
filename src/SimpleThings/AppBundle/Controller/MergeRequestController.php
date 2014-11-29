<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Badge\ScoreCalculator;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Repository\CommitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/merge-request")
 */
class MergeRequestController extends Controller
{
    /**
     * @Route("/{id}/show", name="mergerequest_show")
     */
    public function showAction(MergeRequest $mergeRequest)
    {
        /** @var EntityRepository $repository */
        $repository = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\Commit');
        $commits    = $repository->findBy(['mergeRequest' => $mergeRequest], ['id' => 'DESC']);

        return $this->render(
            "SimpleThingsAppBundle:MergeRequest:show.html.twig",
            [
                'merge_request' => $mergeRequest,
                'commits'       => $commits,
            ]
        );
    }

    /**
     * @Route("/{id}/last-commit", name="mergerequest_lastcommit")
     */
    public function lastCommitAction(MergeRequest $mergeRequest)
    {
        /** @var CommitRepository */
        $repository = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\Commit');
        $commit     = $repository->findLastForMergeRequest($mergeRequest);

        if ( ! $commit) {
            throw new NotFoundHttpException();
        }

        return $this->redirect($this->generateUrl('commit_show', ['id' => $commit->getId()]));
    }

    /**
     * @Route("/{id}/badge", name="mergerequest_imagebadge")
     *
     * @param MergeRequest $mergeRequest
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function badgeAction(MergeRequest $mergeRequest)
    {
        /** @var ScoreCalculator $scoreCalculator */
        $scoreCalculator = $this->get('simple_things_app.badge.score_calculator');
        $score           = $scoreCalculator->get($mergeRequest->getLastCommit());

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
