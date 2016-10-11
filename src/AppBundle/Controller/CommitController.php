<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Commit;
use Pinq\Traversable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use SimpSpector\Analyser\Diff\Calculator;
use SimpSpector\Analyser\Issue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

/**
 * @Framework\Route("/commit")
 *
 * @author Tobias Olry <tobias.olry@gmail.com>
 * @author David Badura <d.a.badura@gmail.com>
 */
class CommitController extends Controller
{
    /**
     * @Framework\Route("/{id}/show", name="commit_show")
     *
     * @param Commit $commit
     *
     * @return array
     */
    public function showAction(Commit $commit)
    {
        return $this->redirectToRoute('commit_detail', [
            'id' => $commit->getId()
        ]);
    }

    /**
     * @Framework\Route("/{id}/detail", name="commit_detail")
     * @Framework\Template()
     *
     * @param Commit $commit
     *
     * @return array
     */
    public function detailAction(Commit $commit)
    {
        return [
            'commit' => $commit,
            'issues' => $this->groupIssues($commit->getIssues())
        ];
    }

    /**
     * @param Commit $commit
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logAction(Commit $commit)
    {
        $reader = $this->get('simpspector.app.logger.reader');
        $log = $reader->getContent($commit);

        return $this->render(
            'AppBundle:Commit:log.html.twig',
            [
                'commit' => $commit,
                'log' => $log
            ]
        );
    }

    /**
     * @Framework\Route("/{from}/diff/{to}", name="commit_diff")
     * @Framework\Template()
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function diffAction($from, $to)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $repo = $em->getRepository('AppBundle:Commit');

        $from = $repo->findOneByRevision($from);
        $to = $repo->findOneByRevision($to);

        if (!$from || !$to) {
            $this->createNotFoundException('Commit not found');
        }

        $calculator = new Calculator();
        $diff = $calculator->diff($from->getResult(), $to->getResult());

        return [
            'from' => $from,
            'to' => $to,
            'diff' => $diff,
            'newIssues' => $this->groupIssues($diff->newIssues),
            'resolvedIssues' => $this->groupIssues($diff->resolvedIssues)
        ];
    }

    /**
     * @Framework\Route("/{id}")
     *
     * @param Commit $commit
     *
     * @return Response
     */
    public function apiAction(Commit $commit)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $json = $serializer->serialize($commit, 'json', ['groups' => ['commit_full']]);

        return new Response($json, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param array $issues
     * @return \Pinq\ITraversable
     */
    protected function groupIssues(array $issues)
    {
        return Traversable::from($issues)
            ->groupBy(function (Issue $issue) {
                return $issue->getFile();
            });
    }
}
