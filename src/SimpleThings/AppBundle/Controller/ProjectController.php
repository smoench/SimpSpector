<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use SimpleThings\AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/{id}/show", name="project_show")
     */
    public function showAction(Project $project)
    {
        /** @var EntityRepository $repository */
        $repository    = $this->get('doctrine')->getRepository('SimpleThings\AppBundle\Entity\MergeRequest');
        $mergeRequests = $repository->findBy(['project' => $project], ['id' => 'DESC']);

        return $this->render(
            "SimpleThingsAppBundle:Project:show.html.twig",
            [
                'project'        => $project,
                'merge_requests' => $mergeRequests,
            ]
        );
    }
}
