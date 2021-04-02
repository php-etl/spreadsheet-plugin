<?php declare(strict_types=1);

namespace Kiboko\Plugin\Spreadsheet\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Extractor implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('extractor');

        $builder->getRootNode()
            ->validate()
                ->ifTrue(function (array $value) {
                    return array_key_exists('excel', $value) && array_key_exists('open_document', $value) && array_key_exists('csv', $value)
                        || array_key_exists('excel', $value) && array_key_exists('open_document', $value)
                        || array_key_exists('excel', $value) && array_key_exists('csv', $value)
                        || array_key_exists('open_document', $value) && array_key_exists('csv', $value);
                })
                ->thenInvalid('Your configuration should either contain the "excel", the "open_document" key or the "csv" key, not many.')
            ->end()
            ->children()
                ->scalarNode('file_path')->isRequired()->end()
                ->arrayNode('excel')
                    ->children()
                        ->scalarNode('sheet')->isRequired()->end()
                        ->integerNode('skip_lines')->defaultValue(0)->end()
                    ->end()
                ->end()
                ->arrayNode('open_document')
                    ->children()
                        ->scalarNode('sheet')->isRequired()->end()
                        ->integerNode('skip_lines')->defaultValue(0)->end()
                    ->end()
                ->end()
                ->arrayNode('csv')
                    ->children()
                        ->integerNode('skip_lines')->defaultValue(0)->end()
                        ->scalarNode('delimiter')->end()
                        ->scalarNode('enclosure')->end()
                        ->scalarNode('encoding')->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
