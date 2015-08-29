<?php

namespace AppBundle\Fixture;

use DavidBadura\GitWebhooks\Struct\Repository;

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
}
