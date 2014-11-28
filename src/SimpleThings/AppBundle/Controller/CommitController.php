<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use SimpleThings\AppBundle\Entity\Commit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/commit")
 */
class CommitController extends Controller
{
    /**
     * @Route("/{id}/show", name="commit_show")
     */
    public function showAction(Commit $commit)
    {
        return $this->render(
            "SimpleThingsAppBundle:Commit:show.html.twig",
            ['commit' => $commit]
        );
    }
}
