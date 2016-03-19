<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace AppBundle\Worker;

use AppBundle\Entity\Project;
use DavidBadura\GitWebhooks\Struct\Commit;
use DavidBadura\GitWebhooks\Event\PushEvent;
use DavidBadura\GitWebhooks\Struct\Repository;
use DavidBadura\GitWebhooks\Struct\User;
use GitElephant\Repository as GitRepository;

class EventFactory
{
    /**
     * @param string $commitHash
     * @param string $gitPath
     * @param Project $project
     *
     * @return PushEvent
     */
    public function createPushBranchEvent($commitHash, $gitPath, Project $project)
    {
        $git = GitRepository::open($gitPath);
        $commitInformation = $git->getCommit($commitHash);

        $event = new PushEvent();
        $event->type = PushEvent::TYPE_BRANCH;
        $event->branchName = 'master';
        $event->repository = new Repository();

        $event->repository = new Repository();
        $event->repository->id  = $project->getRemoteId();
        $event->repository->url = $project->getRepositoryUrl();

        $info = explode('/', $project->getName());
        if (count($info) === 2) {
            $event->repository->namespace = $info[0];
            $event->repository->name      = $info[1];
        } else {
            $event->repository->name = $info[0];
        }

        $commit = new Commit();
        $commit->id = $commitHash;
        $commit->message = $commitInformation->getMessage();
        $commit->date = $commitInformation->getDatetimeAuthor();

        $commit->author = new User();
        $commit->author->email = $commitInformation->getAuthor()->getEmail();
        $commit->author->name = $commitInformation->getAuthor()->getName();

        $event->commits = [$commit];

        return $event;
    }
}
