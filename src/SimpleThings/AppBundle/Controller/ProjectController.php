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
        return $this->render(
            "SimpleThingsAppBundle:Project:show.html.twig",
            ['project' => $project]
        );
    }
}
