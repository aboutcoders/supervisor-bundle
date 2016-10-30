<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Tests\Funcional\Command;

use Abc\Bundle\SupervisorBundle\Command\ProcessStatusCommand;
use Abc\Bundle\SupervisorBundle\Supervisor\Process;
use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Abc\Bundle\SupervisorBundle\Test\CommandTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessStatusCommandTest extends CommandTestCase
{
    /**
     * @dataProvider provideStatusValues
     * @param int $state
     * @param string $statename
     */
    public function testAExecute($state, $statename)
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('ProcessName');

        $process->expects($this->atLeastOnce())
            ->method('getState')
            ->willReturn($state);

        $process->expects($this->atLeastOnce())
            ->method('getStatename')
            ->willReturn($statename);

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->once())
            ->method('getProcesses')
            ->willReturn([$process]);

        $this->manager->expects($this->once())
            ->method('findAll')
            ->willReturn([$supervisor]);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $statusCode    = $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertEquals(0, $statusCode);

        $output = $commandTester->getDisplay();
        $this->assertContains(sprintf('[%s] ProcessName', $statename), $output);
    }

    public function testExecuteWithInvalidProcessName()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->any())
            ->method('getName')
            ->willReturn('Foobar');

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->once())
            ->method('getProcess')
            ->willThrowException(new \InvalidArgumentException('exception message'));

        $this->manager->expects($this->once())
            ->method('findAll')
            ->willReturn([$supervisor]);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $statusCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--process' => 'undefined'
        ));

        $this->assertEquals(1, $statusCode);

        $output = $commandTester->getDisplay();
        $this->assertContains('exception message', $output);
    }

    public function testExecuteWithInvalidGroupName()
    {
        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->once())
            ->method('getProcesses')
            ->with('foobar')
            ->willReturn([]);

        $this->manager->expects($this->once())
            ->method('findAll')
            ->willReturn([$supervisor]);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $statusCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--group' => 'foobar'
        ));

        $this->assertEquals(0, $statusCode);

        $output = $commandTester->getDisplay();
        $this->assertContains('A process with group "foobar" does not exist', $output);
    }

    /**
     * @return array
     */
    public static function provideStatusValues()
    {
        return [
            [\Supervisor\Process::FATAL, 'FATAL'],
            [\Supervisor\Process::BACKOFF, 'BACKOFF'],
            [\Supervisor\Process::EXITED, 'EXITED'],
            [\Supervisor\Process::RUNNING, 'RUNNING'],
            [\Supervisor\Process::STARTING, 'STARTING'],
            [\Supervisor\Process::STOPPING, 'STOPPING'],
            [\Supervisor\Process::UNKNOWN, 'UNKNOWN'],
            [\Supervisor\Process::STOPPED, 'STOPPED'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommand()
    {
        return new ProcessStatusCommand();
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName()
    {
        return 'abc:supervisor:process:status';
    }
}