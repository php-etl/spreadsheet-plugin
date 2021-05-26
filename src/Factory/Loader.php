<?php declare(strict_types=1);


namespace Kiboko\Plugin\Spreadsheet\Factory;

use Kiboko\Contract\Configurator\InvalidConfigurationException;
use Kiboko\Plugin\Spreadsheet;
use Kiboko\Contract\Configurator;
use PhpParser\ParserFactory;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception as Symfony;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use PhpParser\Node;

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

    private function compileValue(string|Expression $value): Node\Expr
    {
        if (is_string($value)) {
            return new Node\Scalar\String_(value: $value);
        }
        if ($value instanceof Expression) {
            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, null);
            return $parser->parse('<?php ' . $this->interpreter->compile($value, ['input']) . ';')[0]->expr;
        }

        throw new InvalidConfigurationException(
            message: 'Could not determine the correct way to compile the provided filter.',
        );
    }

    public function compile(array $config): Repository\Loader
    {
        if (array_key_exists('excel', $config)) {
            $builder = new Spreadsheet\Builder\Excel\Loader(
                $this->compileValue($config['file_path']),
                $this->compileValue($config['excel']['sheet'])
            );
        } elseif (array_key_exists('open_document', $config)) {
            $builder = new Spreadsheet\Builder\OpenDocument\Loader(
                $this->compileValue($config['file_path']),
                $this->compileValue($config['open_document']['sheet'])
            );
        } elseif (array_key_exists('csv', $config)) {
            $builder = new Spreadsheet\Builder\CSV\Loader(
                $this->compileValue($config['file_path']),
                $this->compileValue($config['csv']['delimiter']),
                $this->compileValue($config['csv']['enclosure'])
            );
        } else {
            throw new InvalidConfigurationException(
                'Could not determine if the factory should build an excel, an open_document or a csv loader.'
            );
        }

        return new Repository\Loader($builder);
    }
}
