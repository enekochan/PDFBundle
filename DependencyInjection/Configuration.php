<?php

namespace BushidoIO\PDFBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bushidoio_pdf');

        $this->addOptions($rootNode);

        return $treeBuilder;
    }
    
    /**
     * Add options to the configuration tree
     *
     * @param ArrayNodeDefinition $node
     */
    private function addOptions(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('tmp')
                    ->defaultValue('')
                    ->info('TMP Path')
                    ->example('/var/tmp/')
                ->end()
            ->end()
            ->children()
                ->scalarNode('ttffontdatapath')
                    ->defaultValue('')
                    ->info('TTF Font Data Path')
                    ->example('/var/ttffontdatapath/')
                ->end()
            ->end()
        ;
    }
}
