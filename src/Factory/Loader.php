<?php


namespace Kiboko\Plugin\Spreadsheet\Factory;

use Kiboko\Plugin\Spreadsheet;
use Kiboko\Contract\Configurator;
use PhpParser\Node;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;

final class Loader implements Configurator\FactoryInterface
{
    private Processor $processor;
    private ConfigurationInterface $configuration;

    public function __construct()
    {
        $this->processor = new Processor();
        $this->configuration = new Spreadsheet\Configuration\Loader();
    }

    public function configuration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function normalize(array $config): array
    {
        try {
            return $this->processor->processConfiguration($this->configuration, $config);
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException $exception) {
            throw new Configurator\InvalidConfigurationException($exception->getMessage(), 0, $exception);
        }
    }

    public function validate(array $config): bool
    {
        try {
            if ($this->normalize($config)) {
                return true;
            }
        } catch (\Exception $exception) {
            throw new Configurator\InvalidConfigurationException($exception->getMessage(), 0, $exception);
        }

        return false;
    }

    public function compile(array $config): Repository\Loader
    {
        $builder = new Spreadsheet\Builder\Loader(
            new Node\Scalar\String_($config['file_path']),
            //new Node\Expr\Array_($config['sheets']),
            new Node\Scalar\String_($config['delimiter']),
            new Node\Scalar\String_($config['enclosure']),
            new Node\Scalar\String_($config['escape'])
        );

        return new Repository\Loader($builder);
    }
}
