<?php

namespace Zitec\FormAutocompleteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Defines the structure of the bundle configuration.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zitec_form_autocomplete');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('autocomplete_path_prefix')
                ->defaultValue('/autocomplete')
                ->cannotBeEmpty()
            ->end();

        return $treeBuilder;
    }
}
