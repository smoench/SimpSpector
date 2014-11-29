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

        $files = $this->findPhpFiles($workspace->path, $folders);

        $issues = [];
        foreach ($files as $file) {
            try {
                $fileIssues = array_merge(
                    $this->runPhpParser($file, $parser, $traverser, $visitor, $workspace),
                    $this->runCommentParser($file)
                );

                usort(
                    $fileIssues,
                    function ($a, $b) {
                        return $a->getLine() - $b->getLine();
                    }
                );

                $issues = array_merge($issues, $fileIssues);
            } catch (\Exception $e) {
                $visitor->addException($e);
            }
        }

        return $issues;
    }

    private function runPhpParser($file, Parser $parser, NodeTraverser $traverser, Visitor $visitor, Workspace $workspace)
    {
        $visitor->setCurrentFile($this->cleanupFilePath($workspace, $file));
        $statements = $parser->parse(file_get_contents($file));
        $traverser->traverse($statements);

        return $visitor->flushIssues();
    }

    private function runCommentParser($file)
    {
        return [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'extra';
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

    private function findPhpFiles($path, array $folders)
    {
        $cwd = getcwd();
        chdir($path);

        $finder = (new Finder())
            ->files()
            ->name('*.php')
            ->in($folders);
        $files = iterator_to_array($finder);
        $files = array_map(
            function ($file) {
                return $file->getRealpath();
            },
            $files
        );

        chdir($cwd);

        return $files;
    }
}
