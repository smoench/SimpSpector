<?php
namespace SimpleThings\AppBundle\Gadget;

use PhpParser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\Lexer;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use SimpleThings\AppBundle\Gadget\SimpSpectorExtra\Visitor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\ProcessBuilder;

/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class SimpSpectorExtra extends AbstractGadget
{
    /**
     * @param Workspace $workspace
     * @return Issue[]
     * @throws \Exception
     */
    public function run(Workspace $workspace)
    {
        $visitorOptions = (array)\igorw\get_in($workspace->config, ['extra', 'values'], []);
        $folders        = (array)\igorw\get_in($workspace->config, ['extra', 'files'], ['.']);

        $parser    = new Parser(new Lexer());
        $visitor   = new Visitor($visitorOptions);
        $traverser = new NodeTraverser();

        $traverser->addVisitor($visitor);

        chdir($workspace->path);
        $finder = (new Finder())
            ->files()
            ->name('*.php')
            ->in($folders);

        foreach ($finder as $file) {
            try {
                $file = $file->getRealpath();

                $visitor->setCurrentFile($file);
                $statements = $parser->parse(file_get_contents($file));
                $traverser->traverse($statements);
            } catch (\Exception $e) {
                $visitor->addException($e);
            }
        }

        return $visitor->getIssues();
    }

    /**
     * @return string
     */
    public function getName()
    {

        return 'phpcs';
    }

    /**
     * @param array $options
     * @return array
     */
    private function prepareOption(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'files'     => './',
            'standards' => ['PSR1', 'PSR2']
        ]);

        $resolver->setNormalizers([
            'files'     => function (Options $options, $value) {
                return is_array($value) ? $value : [$value];
            },
            'standards' => function (Options $options, $value) {
                return is_array($value) ? $value : [$value];
            },
        ]);

        return $resolver->resolve($options);
    }

    /**
     * @param string $csv
     * @return array
     */
    private function convertFromCsvToArray($csv)
    {
        $lines = explode(PHP_EOL, $csv);

        $header = array_map('strtolower', str_getcsv(array_shift($lines)));

        $result = [];
        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }

            $result[] = array_combine($header, str_getcsv($line));
        }

        return $result;
    }

    /**
     * @param array $data
     * @return Issue
     */
    private function createIssue(Workspace $workspace, array $data)
    {
        $issue = new Issue($data['message'], 'phpcs');
        $issue->setFile($this->cleanupFilePath($workspace, $data['file']));
        $issue->setLine($data['line']);

        switch ($data['type']) {
            case 'error':
                $issue->setLevel(Issue::LEVEL_ERROR);
                break;
            case 'warning':
                $issue->setLevel(Issue::LEVEL_WARNING);
                break;
        }

        $issue->setExtraInformation([
            'source'   => $data['source'],
            'severity' => $data['severity'],
            'column'   => $data['column']
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
