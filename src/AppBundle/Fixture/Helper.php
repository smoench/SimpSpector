<?php

namespace AppBundle\Fixture;

use DavidBadura\GitWebhooks\Struct\Commit;
use DavidBadura\GitWebhooks\Struct\Repository;
use DavidBadura\GitWebhooks\Struct\User;

class Helper
{
    /**
     * @param string $url
     * @return Repository
     */
    public function generateRepositoryByUrl($url)
    {
        $repository = new Repository();

        $repository->id  = $this->generateRemoteIdByUrl($url);
        $repository->url = $url;

        $info = $this->extractNameAndNamespaceFromUrl($url);
        if (count($info) === 2) {
            $repository->namespace = $info[0];
            $repository->name      = $info[1];
        } else {
            $repository->name = $info[0];
        }

        return $repository;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function generateRemoteIdByUrl($url)
    {
        return hash('sha256', $url);
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private function extractNameAndNamespaceFromUrl($url)
    {
        return explode('/', str_replace('.git', '', trim(parse_url($url)['path'], '/')), 2);
    }

    /**
     * @return string
     */
    public function getRandomVersionNumber()
    {
        return sprintf('%d.%d.%d', max(0, rand(-5, 5)), rand(0, 20), rand(0, 99));
    }

    /**
     * @param string $commitHash
     * @return Commit
     */
    public function generateCommit($commitHash)
    {
        $commit = new Commit();

        $commit->id      = $commitHash;
        $commit->message = 'Test-Message for Commit ' . $commitHash;
        $commit->date    = new \DateTime('-' . rand(2, 6) . 'days'); // todo correct timestamp

        $commit->author        = new User();
        $commit->author->email = 'fabien@symfony.com';
        $commit->author->name  = 'fabpot';

        return $commit;
    }
}
