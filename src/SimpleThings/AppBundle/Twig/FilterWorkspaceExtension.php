<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Twig;

/**
 * @author Tobias Olry <tobias.olrt@gmail.com>
 */
class FilterWorkspaceExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $gitRepositoryTemporaryDirectory;

    /**
     * @param string $gitRepositoryTemporaryDirectory
     */
    public function __construct($gitRepositoryTemporaryDirectory)
    {
        $this->gitRepositoryTemporaryDirectory = rtrim($gitRepositoryTemporaryDirectory, '/');
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            'filter_workspace' => new \Twig_SimpleFilter('filter_workspace', [$this, 'filterWorkspace'])
        ];
    }

    /**
     * @param string $file
     * @return string
     */
    public function filterWorkspace($file)
    {
        $regex = '(' . preg_quote($this->gitRepositoryTemporaryDirectory) . '/\d+_\d+_[\da-f]+/)i';

        return preg_replace($regex, '', $file);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'workspace';
    }
}
