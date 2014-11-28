<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Workspace;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Phpcs extends AbstractGadget
{
    /**
     * @param Workspace $workspace
     * @return mixed
     * @throws \Exception
     */
    public function run(Workspace $workspace)
    {
        $options = $this->prepareOption((array)$workspace->config['phpcs']);

        $processBuilder = new ProcessBuilder(['phpcs', '--report=csv']);

        foreach ($options['standards'] as $standard) {
            $processBuilder->add('--standard=' . $standard);
        }

        foreach ($options['files'] as $file) {
            $processBuilder->add($file);
        }

        $processBuilder->setWorkingDirectory($workspace->path);

        $process = $processBuilder->getProcess();
        $process->setTimeout(3600);

        if ($process->run() !== 0) {
            throw new \Exception($process->getErrorOutput());
        }

        return $this->convertFromCsvToArray($process->getOutput());
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
            'files'     => function ($value) {
                return is_array($value) ? $value : [$value];
            },
            'standards' => function ($value) {
                return is_array($value) ? $value : [$value];
            },
        ]);

        return $resolver->resolve($options);
    }

    /**
     * @param string $xml
     * @return array
     */
    private function convertFromCsvToArray($xml)
    {
        $handler = fopen($xml, 'r');
        $header = array_map('strtolower', fgetcsv($handler));

        $result = [];
        while ($row = fgetcsv($handler) !== false) {
            $result[] = array_combine($header, $row);
        }

        return $result;
    }
}