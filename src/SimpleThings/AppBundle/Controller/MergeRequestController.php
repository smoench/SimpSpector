<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SimpleThings\AppBundle\Entity\MergeRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $repository = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\Commit');
        $commits    = $repository->findBy(['mergeRequest' => $mergeRequest], ['id' => 'DESC']);

        return $this->render(
            "SimpleThingsAppBundle:MergeRequest:show.html.twig",
            [
                'merge_request' => $mergeRequest,
                'commits'      => $commits,
            ]
        );
    }

    /**
     * @Route("/{id}/last-commit", name="mergerequest_lastcommit")
     */
    public function lastCommitAction(MergeRequest $mergeRequest)
    {
        $repository = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\Commit');
        $commit     = $repository->findLastForMergeRequest($mergeRequest);

        if ( ! $commit) {
            throw new NotFoundHttpException();
        }

        return $this->redirect($this->generateUrl('commit_show', ['id' => $commit->getId()]));
    }
}
