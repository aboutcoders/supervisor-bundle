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
use fXmlRpc\Client;
use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Supervisor\Connector\XmlRpc;
use Supervisor\Process;
use Supervisor\Supervisor;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    public function testSupervisor()
    {
        /**
         * @var SupervisorManager $manager
         */
        $manager  = $this->container->get('abc.supervisor.manager');

        var_dump($manager->findByKey('localhost')->getClient()->getAllProcesses());
    }

    /**
     * @return array
     */
    public function provideServices()
    {
        return [
            ['validator', ValidatorInterface::class],
        ];
    }
}