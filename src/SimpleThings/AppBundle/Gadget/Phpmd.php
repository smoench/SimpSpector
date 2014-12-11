<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Phpmd extends AbstractGadget
{
    const NAME = 'phpmd';

    /**
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     * @return Issue[]
     */
    public function run(Workspace $workspace, AbstractLogger $logger)
    {
        $options = $this->prepareOptions(
            (array)$workspace->config[self::NAME],
            [
                'files'    => './',
                'rulesets' => ['codesize', 'unusedcode']
            ],
            ['files', 'rulesets']
        );

        $processBuilder = new ProcessBuilder(['phpmd']);
        $processBuilder->add(implode(',', $options['files']));
        $processBuilder->add('xml');
        $processBuilder->add(implode(',', $options['rulesets']));
        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);

        $process->run(
            function ($type, $buffer) use ($logger) {
                $logger->write($buffer);
            }
        );

        $output = $process->getOutput();

        $result = $this->convertFromXmlToArray($output);

        if (!isset($result['file']) || !is_array($result['file'])) {
            return [];
        }

        $issues = [];

        $files = (isset($result['file'][0])) ? $result['file'] : [$result['file']];
        foreach ($files as $file) {
            $violations = (isset($file['violation'][0])) ? $file['violation'] : [$file['violation']];

            foreach ($violations as $violation) {
                $issues[] = $this->createIssue($workspace, $file['@name'], $violation);
            }
        }

        return $issues;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param string $xml
     * @return array
     */
    private function convertFromXmlToArray($xml)
    {
        $encoder = new XmlEncoder('pmd');

        return $encoder->decode($xml, 'xml');
    }

    /**
     * @param Workspace $workspace
     * @param string    $file
     * @param array     $data
     * @return Issue
     */
    private function createIssue(Workspace $workspace, $file, array $data)
    {
        $issue = new Issue(trim($data['#']), self::NAME, Issue::LEVEL_WARNING);
        $issue->setFile($this->cleanupFilePath($workspace, $file));
        $issue->setLine($data['@beginline']);

        $issue->setExtraInformation(
            [
                'rule'            => $data['@rule'],
                'ruleset'         => $data['@ruleset'],
                'externalInfoUrl' => $data['@externalInfoUrl'],
                'priority'        => $data['@priority']
            ]
        );

        return $issue;
    }
}
