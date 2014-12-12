<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Workspace;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author David Badura <d.a.badura@gmail.com>
 * @author Tobias Olry <tobias.olry@gmail.com>
 * @author Lars Wallenborn <lars@wallenborn.net>
 */
abstract class AbstractGadget implements GadgetInterface
{
    /**
     * @param Workspace $workspace
     * @return bool
     */
    public function isActive(Workspace $workspace)
    {
        return array_key_exists($this->getName(), $workspace->config)
        && $workspace->config[$this->getName()] !== false;
    }

    /**
     * @param Workspace $workspace
     * @param string $file
     * @return string
     */
    protected function cleanupFilePath(Workspace $workspace, $file)
    {
        return ltrim(str_replace($workspace->path, '', $file), '/');
    }

    /**
     * @param string $path
     * @param string[] $folders
     * @param string $pattern
     * @return array
     */
    protected function findFiles($path, array $folders, $pattern = '*.php')
    {
        $cwd = getcwd();
        chdir($path);

        $finder = (new Finder())
            ->files()
            ->name($pattern)
            ->in($folders);

        $files = array_map(
            function ($file) {
                return $file->getRealpath();
            },
            iterator_to_array($finder)
        );

        chdir($cwd);

        return $files;
    }

    /**
     * @param array $options
     * @param string[] $defaults
     * @param string[] $fieldsToNormalize
     * @return array
     */
    protected function prepareOptions(array $options, array $defaults, array $fieldsToNormalize)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults($defaults);

        $normalizers = [];
        foreach ($fieldsToNormalize as $field) {
            $normalizers[$field] = function (Options $options, $value) {
                return is_array($value) ? $value : [$value];
            };
        }
        $resolver->setNormalizers($normalizers);

        return $resolver->resolve($options);
    }
}
