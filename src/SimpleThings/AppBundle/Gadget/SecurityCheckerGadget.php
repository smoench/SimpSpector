<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class SecurityCheckerGadget extends AbstractGadget
{
    const NAME = 'security-checker';

    /**
     * @var string
     */
    private $bin;

    /**
     * @param string $bin
     */
    public function __construct($bin = 'security-checker')
    {
        $this->bin = $bin;
    }

    /**
     * @param Workspace $workspace
     * @return Result
     * @throws \Exception
     */
    public function run(Workspace $workspace)
    {
        $options = $this->prepareOptions(
            (array)$workspace->config[self::NAME],
            [
                'directory' => './',
            ]
        );

        $processBuilder = new ProcessBuilder([$this->bin, '--format=json', $options['directory']]);
        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);

        $process->run();
        $output = $process->getOutput();

        $data   = json_decode($output, true);
        $result = new Result();

        if (count($data) == 0) {
            return $result;
        }

        foreach ($data as $lib => $info) {
            $result->merge($this->createIssues(rtrim($options['directory'], '/') . '/composer.json', $lib, $info));
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param string $composer
     * @param string $lib
     * @param array $info
     * @return Result
     */
    private function createIssues($composer, $lib, array $info)
    {
        $result = new Result();

        foreach ($info['advisories'] as $advisory) {
            $result->addIssue($this->createIssue($composer, $lib, $info['version'], $advisory));
        }

        return $result;
    }

    /**
     * @param $composer
     * @param string $lib
     * @param $version
     * @param array $advisory
     * @return Issue
     */
    private function createIssue($composer, $lib, $version, array $advisory)
    {
        $message = sprintf('package "%s" with the version "%s" have known vulnerabilities', $lib, $version);

        $issue = new Issue($message, self::NAME);

        $issue->setDescription(
            $this->createDescription(
                $advisory['title'],
                $advisory['cve'],
                $advisory['link']
            )
        );

        $issue->setFile($composer);
        $issue->setLevel(Issue::LEVEL_CRITICAL);

        $issue->setExtraInformation(
            [
                'lib'     => $lib,
                'version' => $version,
                'link'    => $advisory['link'],
                'cve'     => $advisory['cve']
            ]
        );

        return $issue;
    }

    /**
     * @param string $title
     * @param string $link
     * @return string
     */
    private function createDescription($title, $cve, $link)
    {
        sprintf("<a href='%s' target='_blank'>%s:</a> %s", $link, $cve, $title);
    }
}
