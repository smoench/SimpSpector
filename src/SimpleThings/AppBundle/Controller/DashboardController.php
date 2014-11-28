<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SimpleThings\AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $repository = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\Project');
        $projects   = $repository->findBy([], ['id' => 'DESC']);

        return $this->render(
            "SimpleThingsAppBundle:Dashboard:index.html.twig",
            [
                'projects' => $projects,
            ]
        );
    }
}
