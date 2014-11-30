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
    const NAME = 'extra';

    /**
     * @param Workspace $workspace
     * @return Issue[]
     * @throws \Exception
     */
    public function run(Workspace $workspace)
    {
        $options = $this->prepareOptions($workspace->config[self::NAME]);

        $parser    = new Parser(new Lexer());
        $visitor   = new Visitor($options['blacklist']);
        $traverser = new NodeTraverser();

        $traverser->addVisitor($visitor);

        $files = $this->findPhpFiles($workspace->path, $options['files']);

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
        return self::NAME;
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

    /**
     * @param array $options
     * @return array
     */
    private function prepareOptions(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(
            [
                'blacklist' => [
                    'die'      => 'error',
                    'var_dump' => 'error',
                    'echo'     => 'warning',
                    'dump'     => 'error',
                ],
                'files'     => ['.'],
            ]
        );

        $normalizeArray = function (Options $options, $value) {
                return is_array($value) ? $value : [$value];
        };

        $resolver->setNormalizers(
            [
                'files'     => $normalizeArray,
                'blacklist' => $normalizeArray,
            ]
        );

        return $resolver->resolve($options);
    }
}
