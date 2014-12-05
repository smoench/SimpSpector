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
        $projectRepository = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Project');
        $commitRepository  = $this->getDoctrine()->getRepository('SimpleThings\AppBundle\Entity\Commit');

        $projects = $projectRepository->findBy([], ['id' => 'DESC']);

        $commits = $commitRepository->findGlobalCommits();

        return $this->render(
            "SimpleThingsAppBundle:Dashboard:index.html.twig",
            [
                'projects' => $projects,
                'commits'  => $commits
            ]
        );
    }
}
