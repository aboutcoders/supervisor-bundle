<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Tests\DependencyInjection;

use Abc\Bundle\SupervisorBundle\DependencyInjection\AbcSupervisorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
abstract class AbcSupervisorExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private static $containerCache = array();

    public function testConnections()
    {
        $container = $this->createContainerFromFile('default');

        $this->assertTrue($container->hasDefinition('abc.supervisor.localhost_connector'));
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $file
     * @return mixed
     */
    abstract protected function loadFromFile(ContainerBuilder $container, $file);

    /**
     * @param string $file
     * @param array  $data
     * @param bool   $resetCompilerPasses
     * @return mixed|ContainerBuilder
     */
    protected function createContainerFromFile($file, $data = array(), $resetCompilerPasses = true)
    {
        $cacheKey = md5(get_class($this) . $file . serialize($data));
        if (isset(self::$containerCache[$cacheKey])) {
            return self::$containerCache[$cacheKey];
        }
        $container = $this->createContainer($data);
        $container->registerExtension(new AbcSupervisorExtension());
        $this->loadFromFile($container, $file);

        if ($resetCompilerPasses) {
            $container->getCompilerPassConfig()->setOptimizationPasses(array());
            $container->getCompilerPassConfig()->setRemovingPasses(array());
        }
        $container->compile();

        return self::$containerCache[$cacheKey] = $container;
    }

    /**
     * @param array $data
     * @return ContainerBuilder
     */
    protected function createContainer(array $data = array())
    {
        return new ContainerBuilder(new ParameterBag(array_merge(array(
            'kernel.bundles'     => array('FrameworkBundle' => 'Abc\\Bundle\\SupervisorBundle\\SupervisorBundle'),
            'kernel.cache_dir'   => __DIR__,
            'kernel.debug'       => false,
            'kernel.environment' => 'test',
            'kernel.name'        => 'kernel',
            'kernel.root_dir'    => __DIR__,
        ), $data)));
    }
}