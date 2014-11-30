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

        foreach ($files as $file) {
            try {
                $visitor->setCurrentFile($this->cleanupFilePath($workspace, $file));
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
        return 'extra';
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
