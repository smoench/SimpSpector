<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Gadget\Repository;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Logger\NullLogger;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class GadgetExecutor
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Workspace $workspace
     * @param AbstractLogger $logger
     * @return Issue[]
     */
    public function run(Workspace $workspace, AbstractLogger $logger = null)
    {
        $logger = $logger ?: new NullLogger();

        $gadgets = $this->repository->getSortedGadgets();
        $result  = [];

        $logger->writeln();
        $logger->writeln("Go go gadgets!");
        $logger->writeln();

        foreach ($gadgets as $gadget) {
            if (!$gadget->isActive($workspace)) {
                continue;
            }

            $logger->writeln();
            $logger->writeln("------------------------------------");
            $logger->writeln();

            $logger->writeln(sprintf('run gadget "%s"', $gadget->getName()));
            $logger->writeln();
            $logger->writeln();

            $issues = $gadget->run($workspace, $logger);

            $logger->writeln();
            $logger->writeln();
            $logger->writeln(sprintf('%s issues found', count($issues)));

            $result = array_merge($result, $issues);
        }

        $logger->writeln();
        $logger->writeln("===============================");
        $logger->writeln();

        $logger->writeln(sprintf('%s issues found', count($result)));

        return $result;
    }
} 
