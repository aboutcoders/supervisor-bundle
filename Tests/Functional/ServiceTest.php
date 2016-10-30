<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Tests\Functional;

use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ServiceTest extends KernelTestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->container   = static::$kernel->getContainer();
        $this->application = new Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);
    }

    /**
     * @param string $service
     * @param string $type
     * @dataProvider provideServices
     */
    public function testServices($service, $type)
    {
        $subject = $this->container->get($service);

        $this->assertInstanceOf($type, $subject);
    }

    /**
     * @return array
     */
    public function provideServices()
    {
        return [
            ['abc.supervisor.manager', SupervisorManager::class]
        ];
    }
}