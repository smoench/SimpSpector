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
        /** @var EntityRepository $repository */
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
