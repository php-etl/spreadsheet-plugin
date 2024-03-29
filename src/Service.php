<?php

declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet;

use Kiboko\Contract\Configurator;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

#[Configurator\Pipeline(
    name: 'spreadsheet',
    dependencies: [
        'php-etl/spreadsheet-flow:*',
    ],
    steps: [
        new Configurator\Pipeline\StepExtractor(),
        new Configurator\Pipeline\StepLoader(),
    ],
)]
final readonly class Service implements Configurator\PipelinePluginInterface
{
    private Processor $processor;
    private Configurator\PluginConfigurationInterface $configuration;

    public function __construct(private ExpressionLanguage $interpreter = new ExpressionLanguage())
    {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    public function interpreter(): ExpressionLanguage
    {
        return $this->interpreter;
    }

    public function configuration(): Configurator\PluginConfigurationInterface
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
            $this->processor->processConfiguration($this->configuration, $config);

            return true;
        } catch (Symfony\InvalidTypeException|Symfony\InvalidConfigurationException) {
            return false;
        }
    }

    public function compile(array $config): Factory\Repository\Extractor|Factory\Repository\Loader
    {
        if (\array_key_exists('expression_language', $config)
            && \is_array($config['expression_language'])
            && \count($config['expression_language'])
        ) {
            foreach ($config['expression_language'] as $provider) {
                $this->interpreter->registerProvider(new $provider());
            }
        }

        if (\array_key_exists('extractor', $config)) {
            $extractorFactory = new Factory\Extractor($this->interpreter);

            return $extractorFactory->compile($config['extractor']);
        }
        if (\array_key_exists('loader', $config)) {
            $loaderFactory = new Factory\Loader($this->interpreter);

            return $loaderFactory->compile($config['loader']);
        }
        throw new Configurator\InvalidConfigurationException('Could not determine if the factory should build an extractor or a loader.');
    }
}
