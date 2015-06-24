<?php

namespace AppBundle\Controller;

use Pinq\Traversable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use AppBundle\Entity\Commit;
use SimpSpector\Analyser\Issue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Framework\Route("/commit")
 *
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class CommitController extends Controller
{
    /**
     * @Framework\Route("/{id}/show", name="commit_show")
     * @Framework\Template()
     *
     * @param Commit $commit
     *
     * @return array
     */
    public function showAction(Commit $commit)
    {
        $issues = Traversable::from($commit->getIssues())
            ->groupBy(function (Issue $issue) {
                return $issue->getFile();
            });

        return ['commit' => $commit, 'issues' => $issues];
    }

    /**
     * @param Commit $commit
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logAction(Commit $commit)
    {
        $reader = $this->get('simple_things_app.logger.reader');
        $log    = $reader->getContent($commit);

        return $this->render(
            'SimpleThingsAppBundle:Commit:log.html.twig',
            [
                'commit' => $commit,
                'log'    => $log
            ]
        );
    }
}
