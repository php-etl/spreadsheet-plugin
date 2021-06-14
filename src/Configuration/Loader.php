<?php declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function Kiboko\Component\SatelliteToolbox\Configuration\asExpression;
use function Kiboko\Component\SatelliteToolbox\Configuration\isExpression;

final class Loader implements ConfigurationInterface
{
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $builder = new TreeBuilder('loader');

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
                        ->integerNode('max_lines')
                            ->min(1)
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
                        ->integerNode('max_lines')
                            ->min(1)
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('csv')
                    ->children()
                        ->scalarNode('delimiter')
                            ->isRequired()
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
                        ->scalarNode('enclosure')
                            ->isRequired()
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
