<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Phpmd extends AbstractGadget
{
    /**
     * @param Workspace $workspace
     * @return Issue[]
     * @throws \Exception
     */
    public function run(Workspace $workspace)
    {
        $options = $this->prepareOption((array)$workspace->config['phpcs']);

        $processBuilder = new ProcessBuilder(['phpmd']);
        $processBuilder->add(implode(',', $options['files']));
        $processBuilder->add('xml');
        $processBuilder->add(implode(',', $options['rulesets']));
        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);

        $process->run();
        $output = $process->getOutput();

        $result = $this->convertFromXmlToArray($output);

        if (!isset($result['file']) || !is_array($result['file'])) {
            return [];
        }

        var_dump($result);

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
        return 'phpmd';
    }

    /**
     * @param array $options
     * @return array
     */
    private function prepareOption(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'files'    => './',
            'rulesets' => ['codesize', 'unusedcode']
        ]);

        $resolver->setNormalizers([
            'files'    => function (Options $options, $value) {
                return is_array($value) ? $value : [$value];
            },
            'rulesets' => function (Options $options, $value) {
                return is_array($value) ? $value : [$value];
            },
        ]);

        return $resolver->resolve($options);
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
     * @param string $file
     * @param array $data
     * @return Issue
     */
    private function createIssue(Workspace $workspace, $file, array $data)
    {
        $issue = new Issue(trim($data['#']), 'phpmd', Issue::LEVEL_WARNING);
        $issue->setFile($this->cleanupFilePath($workspace, $file));
        $issue->setLine($data['@beginline']);

        $issue->setExtraInformation([
            'rule'            => $data['@rule'],
            'ruleset'         => $data['@ruleset'],
            'externalInfoUrl' => $data['@externalInfoUrl'],
            'priority'        => $data['@priority']
        ]);

        return $issue;
    }

    /**
     * @param Workspace $workspace
     * @param string $file
     * @return string
     */
    private function cleanupFilePath(Workspace $workspace, $file)
    {
        return ltrim(str_replace($workspace->path, '', $file), '/');
    }
}