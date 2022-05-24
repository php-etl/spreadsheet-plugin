<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Factory;

use function Kiboko\Component\SatelliteToolbox\Configuration\compileValueWhenExpression;
use Kiboko\Contract\Configurator;
use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\Spreadsheet;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Extractor implements Configurator\FactoryInterface
{
    private Processor $processor;
    private ConfigurationInterface $configuration;

    public function __construct(private ExpressionLanguage $interpreter)
    {
        $this->processor = new Processor();
        $this->configuration = new Spreadsheet\Configuration\Extractor();
    }

    public function interpreter(): ExpressionLanguage
    {
        return $this->interpreter;
    }

    public function configuration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * @throws Configurator\ConfigurationExceptionInterface
     */
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
        } catch (\Exception) {
        }

        return false;
    }

    public function compile(array $config): Repository\Extractor
    {
        if (\array_key_exists('excel', $config)) {
            $builder = new Spreadsheet\Builder\Excel\Extractor(
                compileValueWhenExpression($this->interpreter, $config['file_path']),
                compileValueWhenExpression($this->interpreter, $config['excel']['sheet']),
                compileValueWhenExpression($this->interpreter, $config['excel']['skip_lines'])
            );
        } elseif (\array_key_exists('open_document', $config)) {
            $builder = new Spreadsheet\Builder\OpenDocument\Extractor(
                compileValueWhenExpression($this->interpreter, $config['file_path']),
                compileValueWhenExpression($this->interpreter, $config['open_document']['sheet']),
                compileValueWhenExpression($this->interpreter, $config['open_document']['skip_lines'])
            );
        } elseif (\array_key_exists('csv', $config)) {
            $builder = new Spreadsheet\Builder\CSV\Extractor(
                compileValueWhenExpression($this->interpreter, $config['file_path']),
                compileValueWhenExpression($this->interpreter, $config['csv']['skip_lines']),
                compileValueWhenExpression($this->interpreter, $config['csv']['delimiter']),
                compileValueWhenExpression($this->interpreter, $config['csv']['enclosure']),
                compileValueWhenExpression($this->interpreter, $config['csv']['encoding'])
            );
        } else {
            throw new InvalidConfigurationException('Could not determine if the factory should build an excel, an open_document or a csv extractor.');
        }

        return new Repository\Extractor($builder);
    }
}
