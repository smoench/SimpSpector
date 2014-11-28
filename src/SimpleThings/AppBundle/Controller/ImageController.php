<?php
/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */

namespace SimpleThings\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SimpleThings\AppBundle\Entity\MergeRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/image")
 */
class ImageController extends Controller
{
    /**
     * @Route("/{merge_request_id}", name="image_badge")
     */
    public function badgeAction(MergeRequest $mergeRequest)
    {
        /* @todo: use $mergeRequest to calculate score */

        $score = 50;

        return new Response($this->renderView("SimpleThingsAppBundle:Image:show.xml.twig", [
            'score' => $score,
            'color' => $this->getColor($score),
        ]), 200, [
            'Content-Type'        => 'image/svg',
            'Content-Disposition' => 'inline; filename="status.svg"'
        ]);
    }

    /**
     * @param int $n
     * @return string
     */
    private function getColor($n)
    {
        $n = 100 - $n;
        $r = (255 * $n) / 100;
        $g = (255 * (100 - $n)) / 100;
        $b = 0;

        return sprintf('%02X%02X%02X', $r, $g, $b);
    }
}
