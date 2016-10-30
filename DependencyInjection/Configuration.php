<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('abc_supervisor');
        $rootNode
            ->children()
                ->arrayNode('connections')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('host')->isRequired()->end()
                        ->scalarNode('port')->defaultValue(9001)->end()
                        ->scalarNode('username')->end()
                        ->scalarNode('password')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}