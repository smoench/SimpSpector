<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Command;

use SimpleThings\AppBundle\Entity\Commit;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class JobRunCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('simpspector:job:run');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commitHandler = $this->getContainer()->get('simple_things_app.commit_handler');
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $commitRepository = $entityManager->getRepository('SimpleThingsAppBundle:Commit');

        foreach ($commitRepository->findNewCommits() as $commit) {
            $output->writeln('job id ' . $commit->getId());

            $commit->setStatus(Commit::STATUS_RUN);
            $entityManager->flush($commit);

            try {
                $commitHandler->handle($commit);
                $commit->setStatus(Commit::STATUS_SUCCESS);
            } catch (\Exception $e) {
                $commit->setStatus(Commit::STATUS_ERROR);
            }

            $entityManager->flush($commit);
        }
    }
}