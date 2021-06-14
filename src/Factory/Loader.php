<?php declare(strict_types=1);


namespace Kiboko\Plugin\Spreadsheet\Factory;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\Spreadsheet;
use Kiboko\Contract\Configurator;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use function Kiboko\Component\SatelliteToolbox\Configuration\compileValueWhenExpression;

final class Loader implements Configurator\FactoryInterface
{
    private Processor $processor;
    private ConfigurationInterface $configuration;

    public function __construct(private ExpressionLanguage $interpreter)
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
        if (array_key_exists('excel', $config)) {
            if (array_key_exists('max_lines', $config["excel"])) {
                $builder = new Spreadsheet\Builder\Excel\MultipleFileLoader(
                    compileValueWhenExpression($this->interpreter, $config['file_path'], 'index'),
                    compileValueWhenExpression($this->interpreter, $config['excel']['sheet']),
                    compileValueWhenExpression($this->interpreter, $config['excel']['max_lines']),
                );
            } else {
                $builder = new Spreadsheet\Builder\Excel\Loader(
                    compileValueWhenExpression($this->interpreter, $config['file_path']),
                    compileValueWhenExpression($this->interpreter, $config['excel']['sheet'])
                );
            }
        } elseif (array_key_exists('open_document', $config)) {
            if (array_key_exists('max_lines', $config["open_document"])) {
                $builder = new Spreadsheet\Builder\OpenDocument\MultipleFileLoader(
                    compileValueWhenExpression($this->interpreter, $config['file_path'], 'index'),
                    compileValueWhenExpression($this->interpreter, $config['open_document']['sheet']),
                    compileValueWhenExpression($this->interpreter, $config['open_document']['max_lines']),
                );
            } else {
                $builder = new Spreadsheet\Builder\OpenDocument\Loader(
                    compileValueWhenExpression($this->interpreter, $config['file_path']),
                    compileValueWhenExpression($this->interpreter, $config['open_document']['sheet'])
                );
            }
        } elseif (array_key_exists('csv', $config)) {
            $builder = new Spreadsheet\Builder\CSV\Loader(
                compileValueWhenExpression($this->interpreter, $config['file_path']),
                compileValueWhenExpression($this->interpreter, $config['csv']['delimiter']),
                compileValueWhenExpression($this->interpreter, $config['csv']['enclosure'])
            );
        } else {
            throw new InvalidConfigurationException(
                'Could not determine if the factory should build an excel, an open_document or a csv loader.'
            );
        }

        return new Repository\Loader($builder);
    }
}
