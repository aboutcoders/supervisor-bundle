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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class AbcSupervisorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $def = $container->getDefinition('abc.supervisor.connector');
        if (method_exists($def, 'setFactory')) {
            $def->setFactory(array(new Reference('abc.supervisor.connector_factory'), 'createConnector'));
        } else {
            $def->setFactoryService('abc.supervisor.connector_factory');
            $def->setFactoryMethod('createConnector');
        }

        $def = $container->getDefinition('abc.supervisor.supervisor');
        if (method_exists($def, 'setFactory')) {
            $def->setFactory(array(new Reference('abc.supervisor.supervisor_factory'), 'createSupervisor'));
        } else {
            $def->setFactoryService('abc.supervisor.supervisor_factory');
            $def->setFactoryMethod('createSupervisor');
        }

        $def = $container->getDefinition('abc.supervisor.manager');
        foreach ($config['connections'] as $name => $connection) {
            $connectorId  = $this->loadConnector($name, $connection, $container);
            $supervisorId = $this->loadSupervisor($name, $connection['host'], $connectorId, $container);
            $def->addMethodCall('add', [new Reference($supervisorId)]);
        }
    }

    /**
     * Loads a configured supervisor connector.
     *
     * @param string           $name       The name of the connection
     * @param array            $connection A supervisor connection configuration.
     * @param ContainerBuilder $container  A ContainerBuilder instance
     * @return string The id of the connector definition
     */
    protected function loadConnector($name, array $connection, ContainerBuilder $container)
    {
        $id = sprintf('abc.supervisor.%s_connector', $name);

        $container
            ->setDefinition($id, new DefinitionDecorator('abc.supervisor.connector'))
            ->setArguments(array($connection));

        return $id;
    }

    /**
     * Loads a configured supervisor connector.
     *
     * @param string           $name        The name of the connection
     * @param string           $host        The name of the host
     * @param string           $connectorId The service id of the connector
     * @param ContainerBuilder $container   A ContainerBuilder instance
     * @return string The id of the supervisor definition
     */
    protected function loadSupervisor($name, $host, $connectorId, ContainerBuilder $container)
    {
        $id = sprintf('abc.supervisor.%s_supervisor', $name);

        $container
            ->setDefinition($id, new DefinitionDecorator('abc.supervisor.supervisor'))
            ->setArguments(array($name, $host, new Reference($connectorId)));

        return $id;
    }
}