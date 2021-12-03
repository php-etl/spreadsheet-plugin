<?php declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function Kiboko\Component\SatelliteToolbox\Configuration\asExpression;
use function Kiboko\Component\SatelliteToolbox\Configuration\isExpression;
use function Kiboko\Component\SatelliteToolbox\Configuration\mutuallyExclusiveFields;

final class Loader implements ConfigurationInterface
{
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $builder = new TreeBuilder('loader');

        $builder->getRootNode()
            ->validate()
                ->always(mutuallyExclusiveFields('excel', 'open_document', 'csv'))
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
                        ->integerNode('max_lines')
                            ->min(1)
                            ->validate()
                                ->ifTrue(isExpression())
                                ->then(asExpression())
                            ->end()
                        ->end()
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
}
