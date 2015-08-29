<?php

namespace AppBundle\Fixture;

class Helper
{
    /**
     * @param string $project
     *
     * @return string
     */
    public static function generateRemoteIdByProjectName($project)
    {
        return hash('sha256', $project);
    }

    /**
     * @param $url
     *
     * @return string
     */
    public static function generateProjectNameByUrl($url)
    {
        return trim(parse_url($url)['path'], '/');
    }
}
