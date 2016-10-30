<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Tests\Controller;

use Abc\Bundle\SupervisorBundle\Controller\SupervisorController;
use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var SupervisorManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $supervisorManager;

    /**
     * @var SupervisorController|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subject;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->supervisorManager = $this->createMock(SupervisorManager::class);

        $this->container = $this->createMock(ContainerInterface::class);
        $services        = ['abc.supervisor.manager' => $this->supervisorManager];

        $this->container->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($key) use ($services) {
                return $services[$key];
            });

        $this->subject = $this->getMockBuilder(SupervisorController::class)
            ->disableOriginalConstructor()
            ->setMethods(['json'])
            ->getMock();

        $this->subject->setContainer($this->container);
    }

    public function testListAction()
    {
        $client = $this->getMockBuilder(\Supervisor\Supervisor::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getAPIVersion',
                'getPID',
            ])
            ->getMock();

        $client->expects($this->once())
            ->method('getAPIVersion')
            ->willReturn('ApiVersion');

        $client->expects($this->once())
            ->method('getPID')
            ->willReturn('PID');

        $supervisor = $this->createMock(Supervisor::class);

        $supervisor->expects($this->any())
            ->method('getClient')
            ->willReturn($client);

        $supervisor->expects($this->once())
            ->method('getId')
            ->willReturn('Id');

        $supervisor->expects($this->once())
            ->method('getHost')
            ->willReturn('Host');

        $supervisor->expects($this->once())
            ->method('getStatus')
            ->willReturn('Status');

        $supervisors = [$supervisor];

        $this->supervisorManager->expects($this->once())
            ->method('findAll')
            ->willReturn($supervisors);

        $this->subject->expects($this->once())
            ->method('json')
            ->with([
                [
                    'id'          => 'Id',
                    'host'        => 'Host',
                    'pid'         => 'PID',
                    'status'      => 'Status',
                    'api_version' => 'ApiVersion']
            ])
            ->willReturn('Result');

        $this->assertEquals('Result', $this->subject->listAction());
    }
}