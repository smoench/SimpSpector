<?php
namespace SimpleThings\AppBundle\Gadget;

use PhpParser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\Lexer;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use SimpleThings\AppBundle\Gadget\FunctionBlacklist\Visitor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class FunctionBlacklistGadget extends AbstractGadget
{
    const NAME = 'function_blacklist';

    /**
     * @param Workspace $workspace
     * @return Result
     * @throws \Exception
     */
    public function run(Workspace $workspace)
    {
        $options = $this->prepareOptions(
            $workspace->config[self::NAME],
            [
                'files'     => ['.'],
                'blacklist' => [
                    'die'      => 'error',
                    'var_dump' => 'error',
                    'echo'     => 'warning',
                    'dump'     => 'error',
                ],
            ],
            ['files', 'blacklist']);

        $result    = new Result();
        $parser    = new Parser(new Lexer());
        $visitor   = new Visitor($options['blacklist'], $result);
        $traverser = new NodeTraverser();

        $traverser->addVisitor($visitor);

        $files = $this->findFiles($workspace->path, $options['files']);

        foreach ($files as $file) {
            try {
                $visitor->setCurrentFile($this->cleanupFilePath($workspace, $file));
                $statements = $parser->parse(file_get_contents($file));
                $traverser->traverse($statements);
            } catch (\Exception $e) {
                $visitor->addException($e);
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
}
