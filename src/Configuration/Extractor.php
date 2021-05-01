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
                ->always($this->mutuallyExclusiveFields('excel', 'open_document', 'csv'))
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
