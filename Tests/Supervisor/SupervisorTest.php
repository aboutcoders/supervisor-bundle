<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Tests\Supervisor;

use Abc\Bundle\SupervisorBundle\Supervisor\Process;
use Abc\Bundle\SupervisorBundle\Supervisor\ProcessInterface;
use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Supervisor\Supervisor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var Supervisor
     */
    private $subject;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->client  = $this->getMockBuilder(\Supervisor\Supervisor::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getState',
                'getAllProcesses',
                'getProcess',
                'startProcess',
                'stopProcess'
            ])
            ->getMock();
        $this->subject = new Supervisor('Id', 'Host', $this->client);
    }

    public function testGetId()
    {
        $this->assertEquals('Id', $this->subject->getId());
    }

    public function testGetHost()
    {
        $this->assertEquals('Host', $this->subject->getHost());
    }

    public function testGetClient()
    {
        return $this->assertSame($this->client, $this->subject->getClient());
    }

    public function testGetStatus()
    {
        $this->client->expects($this->once())
            ->method('getState')
            ->willReturn(['statename' => 'RUNNING', 'state' => 1]);

        $this->assertEquals('RUNNING', $this->subject->getStatus());
    }

    public function testGetProcesses()
    {
        $process   = $this->createMock(\Supervisor\Process::class);
        $processes = [$process];

        $this->client->expects($this->once())
            ->method('getAllProcesses')
            ->willReturn($processes);

        $returnValue = $this->subject->getProcesses();

        $this->assertNotEmpty($returnValue);
        $this->assertInstanceOf(Process::class, $returnValue[0]);
    }

    public function testGetProcessesWithGroup()
    {
        $process   = $this->createMock(\Supervisor\Process::class);
        $processes = [$process];

        $process->expects($this->any())
            ->method('offsetGet')
            ->with('group')
            ->willReturn('foobar');

        $this->client->expects($this->once())
            ->method('getAllProcesses')
            ->willReturn($processes);

        $returnValue = $this->subject->getProcesses('foobar');

        $this->assertNotEmpty($returnValue);
        $this->assertInstanceOf(Process::class, $returnValue[0]);

        $this->assertEmpty($this->subject->getProcesses('barfoo'));
    }

    public function testGetProcess()
    {
        $process   = $this->createMock(\Supervisor\Process::class);
        $processes = [$process];

        $process->expects($this->any())
            ->method('getName')
            ->willReturn('foobar');

        $this->client->expects($this->once())
            ->method('getAllProcesses')
            ->willReturn($processes);

        $returnValue = $this->subject->getProcess('foobar');
        $this->assertInstanceOf(Process::class, $returnValue);
        $this->assertAttributeSame($process, 'process', $returnValue);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetProcessThrowsInvalidArgumentException()
    {
        $this->client->expects($this->once())
            ->method('getAllProcesses')
            ->willReturn([]);

        $this->subject->getProcess('foobar');
    }

    public function testStartProcess()
    {
        $process   = $this->createMock(\Supervisor\Process::class);
        $processes = [$process];

        $process->expects($this->any())
            ->method('getName')
            ->willReturn('ProcessName');

        $process->expects($this->any())
            ->method('offsetGet')
            ->with('group')
            ->willReturn('ProcessGroup');

        $this->client->expects($this->once())
            ->method('getAllProcesses')
            ->willReturn($processes);

        $this->client->expects($this->once())
            ->method('startProcess')
            ->with('ProcessGroup:ProcessName');

        $this->subject->startProcess($this->subject->getProcess('ProcessName'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStartProcessWithProcessNotExists()
    {
        $this->subject->startProcess($this->createMock(ProcessInterface::class));
    }

    public function testStopProcess()
    {
        $process   = $this->createMock(\Supervisor\Process::class);
        $processes = [$process];

        $process->expects($this->any())
            ->method('getName')
            ->willReturn('ProcessName');

        $process->expects($this->any())
            ->method('offsetGet')
            ->with('group')
            ->willReturn('ProcessGroup');

        $this->client->expects($this->once())
            ->method('getAllProcesses')
            ->willReturn($processes);

        $this->client->expects($this->once())
            ->method('stopProcess')
            ->with('ProcessGroup:ProcessName');

        $this->subject->stopProcess($this->subject->getProcess('ProcessName'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStopProcessWithProcessNotExists()
    {
        $this->subject->stopProcess($this->createMock(ProcessInterface::class));
    }

    public function testRefreshProcess()
    {
        $processInterface =$this->createMock(ProcessInterface::class);
        $process = $this->createMock(Process::class);
        $baseProcess = $this->createMock(\Supervisor\Process::class);

        $this->client->expects($this->once())
            ->method('getProcess')
            ->with('ProcessId')
            ->willReturn($baseProcess);

        /**
         * @var Supervisor|\PHPUnit_Framework_MockObject_MockObject $subject
         */
        $subject = $this->getMockBuilder(Supervisor::class)
            ->setConstructorArgs(['Id', 'Host', $this->client])
            ->setMethods(['doGetProcess'])
            ->getMock();

        $subject->expects($this->once())
            ->method('doGetProcess')
            ->with($processInterface)
            ->willReturn($process);

        $process->expects($this->once())
            ->method('getId')
            ->willReturn('ProcessId');

        $process->expects($this->once())
            ->method('setProcess')
            ->with($baseProcess);

        $subject->refreshProcess($processInterface);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRefreshProcessWithProcessNotExists()
    {
        $this->subject->refreshProcess($this->createMock(ProcessInterface::class));
    }
}