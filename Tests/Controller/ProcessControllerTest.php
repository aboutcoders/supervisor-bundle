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

use Abc\Bundle\SupervisorBundle\Controller\ProcessController;
use Abc\Bundle\SupervisorBundle\Supervisor\Process;
use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;
use fXmlRpc\Exception\FaultException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var SupervisorManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * @var ProcessController|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subject;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->manager = $this->createMock(SupervisorManager::class);

        $this->container = $this->createMock(ContainerInterface::class);
        $services        = [
            'abc.supervisor.manager' => $this->manager
        ];

        $this->container->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($key) use ($services) {
                return $services[$key];
            });

        $this->subject = $this->getMockBuilder(ProcessController::class)
            ->disableOriginalConstructor()
            ->setMethods(['json'])
            ->getMock();

        $this->subject->setContainer($this->container);
    }

    public function testListAction()
    {
        $client = $this->getMockBuilder(\Supervisor\Supervisor::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllProcessInfo'])
            ->getMock();

        $client->expects($this->once())
            ->method('getAllProcessInfo')
            ->willReturn(['ProcessInfo']);

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->any())
            ->method('getClient')
            ->willReturn($client);

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn($supervisor);

        $this->subject->expects($this->once())
            ->method('json')
            ->with(['ProcessInfo'])
            ->willReturn('Result');

        $this->assertEquals('Result', $this->subject->listAction('SupervisorId'));
    }

    public function testStartAction()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())
            ->method('toArray')
            ->willReturn(['ProcessInfo']);

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->any())
            ->method('getProcess')
            ->with('ProcessName')
            ->willReturn($process);

        $supervisor->expects($this->once())
            ->method('startProcess')
            ->with($process);

        $supervisor->expects($this->once())
            ->method('refreshProcess')
            ->with($process);

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn($supervisor);

        $this->subject->expects($this->once())
            ->method('json')
            ->with(['ProcessInfo'])
            ->willReturn('Result');

        $this->assertEquals('Result', $this->subject->startAction('SupervisorId', 'ProcessName'));
    }

    public function testStartActionWithSupervisorThrowsException()
    {
        $process    = $this->createMock(Process::class);
        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->any())
            ->method('getProcess')
            ->with('ProcessName')
            ->willReturn($process);

        $supervisor->expects($this->once())
            ->method('startProcess')
            ->with($process)
            ->willThrowException(new FaultException('Exception Message'));

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn($supervisor);

        $this->subject->expects($this->once())
            ->method('json')
            ->with(['error' => 'Exception Message'], 500)
            ->willReturn('Result');

        $this->assertEquals('Result', $this->subject->startAction('SupervisorId', 'ProcessName'));
    }

    public function testStopAction()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())
            ->method('toArray')
            ->willReturn(['ProcessInfo']);

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->any())
            ->method('getProcess')
            ->with('ProcessName')
            ->willReturn($process);

        $supervisor->expects($this->once())
            ->method('stopProcess')
            ->with($process);

        $supervisor->expects($this->once())
            ->method('refreshProcess')
            ->with($process);

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn($supervisor);

        $this->subject->expects($this->once())
            ->method('json')
            ->with(['ProcessInfo'])
            ->willReturn('Result');

        $this->assertEquals('Result', $this->subject->stopAction('SupervisorId', 'ProcessName'));
    }

    public function testStopActionWithSupervisorThrowsException()
    {
        $process    = $this->createMock(Process::class);
        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->any())
            ->method('getProcess')
            ->with('ProcessName')
            ->willReturn($process);

        $supervisor->expects($this->once())
            ->method('stopProcess')
            ->with($process)
            ->willThrowException(new FaultException('Exception Message'));

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn($supervisor);

        $this->subject->expects($this->once())
            ->method('json')
            ->with(['error' => 'Exception Message'], 500)
            ->willReturn('Result');

        $this->assertEquals('Result', $this->subject->stopAction('SupervisorId', 'ProcessName'));
    }

    /**
     * @dataProvider provideActionNames
     * @param string $action
     * @param array  $arguments
     */
    public function testReturns404WithInvalidId($action, array $arguments = [])
    {
        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->subject->expects($this->once())
            ->method('json')
            ->with(['error' => 'Supervisor with id "SupervisorId" not found'], 404)
            ->willReturn('Result');

        $this->assertEquals('Result', call_user_func_array([$this->subject, $action], $arguments));
    }

    /**
     * @return array
     */
    public static function provideActionNames()
    {
        return [
            ['listAction', ['SupervisorId']],
            ['startAction', ['SupervisorId', 'ProcessName']],
            ['stopAction', ['SupervisorId', 'ProcessName']]
        ];
    }
}