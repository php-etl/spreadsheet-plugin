<?php declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function Kiboko\Component\SatelliteToolbox\Configuration\asExpression;
use function Kiboko\Component\SatelliteToolbox\Configuration\isExpression;

final class Extractor implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('extractor');

        $builder->getRootNode()
            ->validate()
                ->always($this->mutuallyExclusiveFields('excel', 'open_document', 'csv'))
            ->end()
            ->children()
                ->scalarNode('file_path')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(isExpression())
                        ->then(asExpression())
                    ->end()
                ->end()
                ->arrayNode('excel')
                    ->children()
                        ->scalarNode('sheet')
                            ->isRequired()
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                        ->scalarNode('skip_lines')
                            ->defaultValue(0)
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('open_document')
                    ->children()
                        ->scalarNode('sheet')
                            ->isRequired()
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                        ->scalarNode('skip_lines')
                            ->defaultValue(0)
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('csv')
                    ->children()
                        ->scalarNode('skip_lines')
                            ->defaultValue(0)
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                        ->scalarNode('delimiter')
                            ->defaultValue(',')
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                        ->scalarNode('enclosure')
                            ->defaultValue('"')
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                        ->scalarNode('encoding')
                            ->defaultValue("UTF-8")
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }

    private function mutuallyExclusiveFields(string $field, string ...$exclusions): \Closure
    {
        return function (array $value) use ($field, $exclusions) {
            if (!array_key_exists($field, $value)) {
                return $value;
            }

            foreach ($exclusions as $exclusion) {
                if (array_key_exists($exclusion, $value)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Your configuration should either contain the "%s" or the "%s" field, not both.',
                        $field,
                        $exclusion,
                    ));
                }
            }

            return $value;
        };
    }
}
