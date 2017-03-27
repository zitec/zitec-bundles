<?php

namespace Zitec\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * The bundle's configuration definition.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('zitec_translation');

        $root
            ->children()
                ->scalarNode('js_translation_domain')
                    ->cannotBeEmpty()
                    ->defaultValue('js')
                    ->info('The default translation domain used in JavaScript.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
