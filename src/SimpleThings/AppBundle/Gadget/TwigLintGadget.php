<?php
namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Logger\AbstractLogger;
use SimpleThings\AppBundle\Entity\Issue;
use SimpleThings\AppBundle\Workspace;
use Asm89\Twig\Lint\StubbedEnvironment;

/**
 * @author Tobias Olry <tobias.olry@gmail.com>
 */
class TwigLintGadget extends AbstractGadget
{
    const NAME = 'twig_lint';

    private $twig;

    public function __construct()
    {
        $this->twig = new StubbedEnvironment(new \Twig_Loader_String());
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
            (array) $workspace->config[self::NAME],
            [
                'files'       => ['.'],
                'error_level' => 'error',
            ],
            ['files']
        );

        $result = new Result();
        $files  = $this->findFiles($workspace->path, $options['files'], '*.twig');

        foreach ($files as $file) {
            try {
                $this->twig->parse($this->twig->tokenize(file_get_contents($file), $file));
            } catch (\Twig_Error $e) {
                $cleanedUpFile = $this->cleanupFilePath($workspace, $file);
                $message       = get_class($e) . ': ' . $e->getRawMessage();

                $issue = new Issue($message, self::NAME, $options['error_level']);
                $issue->setFile($cleanedUpFile);
                $issue->setLine($e->getTemplateLine());

                $result->addIssue($issue);
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
