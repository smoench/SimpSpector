<?php

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Process\ProcessBuilder;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class PhpmdGadget extends AbstractGadget
{
    const NAME = 'phpmd';

    /**
     * @var string
     */
    private $bin;

    /**
     * @param string $bin
     */
    public function __construct($bin = 'phpmd')
    {
        $this->bin = $bin;
    }

    /**
     * @param Workspace      $workspace
     * @param AbstractLogger $logger
     * @return Result
     * @throws \Exception
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

        $processBuilder = new ProcessBuilder([$this->bin]);
        $processBuilder->add(implode(',', $options['files']));
        $processBuilder->add('xml');
        $processBuilder->add(implode(',', $options['rulesets']));
        $processBuilder->setWorkingDirectory($workspace->path);
        $output = $processBuilder->run($logger);

        $data = $this->convertFromXmlToArray($output);

        $result = new Result();

        if ( ! isset($data['file']) || ! is_array($data['file'])) {
            return $result;
        }

        $files = (isset($data['file'][0])) ? $data['file'] : [$data['file']];
        foreach ($files as $file) {
            $violations = (isset($file['violation'][0])) ? $file['violation'] : [$file['violation']];

            foreach ($violations as $violation) {
                $result->addIssue($this->createIssue($workspace, $file['@name'], $violation));
            }
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
