<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace SimpleThings\AppBundle\Controller;

use Pinq\Traversable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SimpleThings\AppBundle\Entity\Commit;
use SimpleThings\AppBundle\Entity\Issue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/commit")
 */
class CommitController extends Controller
{
    /**
     * @Route("/{id}/show", name="commit_show")
     *
     * @param Commit $commit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Commit $commit)
    {
        $issues = Traversable::from($commit->getIssues())
            ->groupBy(function (Issue $issue) { return $issue->getFile(); });

        return $this->render(
            'SimpleThingsAppBundle:Commit:show.html.twig',
            ['commit' => $commit, 'issues' => $issues]
        );
    }
}
