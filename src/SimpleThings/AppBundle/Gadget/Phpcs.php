<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

use SimpleThings\AppBundle\Workspace;
use Symfony\Component\OptionsResolver\Options;
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

        $process->run();
        $output = $process->getOutput();

        return $this->convertFromCsvToArray($output);
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
     * @param string $xml
     * @return array
     */
    private function convertFromCsvToArray($xml)
    {
        $lines = explode(PHP_EOL, $xml);

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
}