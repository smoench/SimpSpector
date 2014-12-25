<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $commitRepository = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Commit');
        $commits          = $commitRepository->findGlobalCommits(10);

        return $this->render(
            "SimpleThingsAppBundle:Dashboard:index.html.twig",
            [
                'commits' => $commits
            ]
        );
    }
}
