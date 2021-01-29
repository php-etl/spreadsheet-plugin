<?php

namespace Kiboko\Plugin\Spreadsheet\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Loader implements ConfigurationInterface
{
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $builder = new TreeBuilder('loader');

        $builder->getRootNode()
            ->children()
                ->scalarNode('file_path')->isRequired()->end()
                /*->arrayNode('sheets')
                    ->scalarPrototype()->end()
                ->end()*/
                ->scalarNode('delimiter')->defaultValue(',')->end()
                ->scalarNode('enclosure')->defaultValue('"')->end()
                ->scalarNode('escape')->defaultValue('\\')->end()
            ->end();

        return $builder;
    }
}
