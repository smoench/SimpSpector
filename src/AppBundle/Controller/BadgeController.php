<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use AppBundle\Entity\MergeRequest;
use AppBundle\Entity\Project;
use SimpSpector\Analyser\Result;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class BadgeController extends Controller
{
    /**
     * @Framework\Route("/merge-request/{id}/badge", name="mergerequest_imagebadge")
     *
     * @param MergeRequest $mergeRequest
     *
     * @return Response
     */
    public function mergeRequestAction(MergeRequest $mergeRequest)
    {
        $commitRepository = $this->get('simpspector.app.repository.commit');

        return $this->generateBadge($commitRepository->findLastForMergeRequest($mergeRequest)->getResult());
    }

    /**
     * @Framework\Route("/projects/{id}/badge", name="project_imagebadge")
     *
     * @param Project $project
     *
     * @return Response
     */
    public function projectAction(Project $project)
    {
        $commitRepository = $this->get('simpspector.app.repository.commit');

        return $this->generateBadge($commitRepository->findLastInMaster($project)->getResult());
    }

    /**
     * @param Result $result
     * @return Response
     */
    private function generateBadge(Result $result)
    {
        $scoreCalculator = $this->get('simpspector.app.score.calculator');
        $score           = $scoreCalculator->calculate($result);

        $response = new Response($this->renderView("AppBundle:Badge:badge.xml.twig", [
            'score' => $score,
            'color' => 'ff0000'
        ]), 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="status.svg"'
        ]);
        $response->setMaxAge(0);
        $response->setExpires(new \DateTime('-1 hour'));

        return $response;
    }
}