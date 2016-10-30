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
use Supervisor\Process as BaseProcess;

class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BaseProcess|\PHPUnit_Framework_MockObject_MockObject
     */
    private $process;

    /**
     * @var Process
     */
    private $subject;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->process = $this->createMock(BaseProcess::class);
        $this->subject = new Process($this->process);
    }

    public function testSetProcess()
    {
        $process = $this->createMock(BaseProcess::class);
        $this->subject->setProcess($process);
        $this->assertAttributeSame($process, 'process', $this->subject);
    }

    public function testGetId()
    {
        $this->process->expects($this->once())
            ->method('getName')
            ->willReturn('ProcessName');

        $this->process->expects($this->any())
            ->method('offsetGet')
            ->with('group')
            ->willReturn('GroupName');

        $this->assertEquals('GroupName:ProcessName', $this->subject->getId());
    }

    /**
     * @dataProvider provideGetterPropertyNames
     * @param string $property
     * @param null   $method
     */
    public function testOffsetGetters($property, $method = null)
    {
        $this->process->expects($this->once())
            ->method('offsetGet')
            ->with($property)
            ->willReturnArgument(0);

        $method = $method == null ? 'get' . ucfirst($property) : $method;
        call_user_func_array([$this->subject, $method], []);
    }

    public function testToArray()
    {
        $this->process->expects($this->once())
            ->method('getPayload')
            ->willReturn(['ToArray']);

        $this->assertEquals(['ToArray'], $this->subject->toArray());
    }

    /**
     * @return array
     */
    public static function provideGetterPropertyNames()
    {
        return [
            ['description'],
            ['group'],
            ['pid'],
            ['uptime'],
            ['logfile'],
            ['stderr_logfile', 'getStderrLogfile'],
            ['stdout_logfile', 'getStdoutLogfile'],
            ['state'],
            ['statename'],
            ['start']
        ];
    }
}