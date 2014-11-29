<?php
/**
 *
 */

namespace SimpleThings\AppBundle;

use SimpleThings\AppBundle\Exception\MissingSimpSpectorConfigException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class ConfigLoader
{
    /**
     * @param Workspace $workspace
     * @throws \Exception
     * @return array
     */
    public function load(Workspace $workspace)
    {
        $configFile = $workspace->path . '/simpspector.yml';

        if (!file_exists($configFile)) {
            throw new MissingSimpSpectorConfigException();
        }

        return Yaml::parse(file_get_contents($configFile));
    }
}
