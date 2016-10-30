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

use Abc\Bundle\SupervisorBundle\Command\ProcessStopCommand;
use Abc\Bundle\SupervisorBundle\Supervisor\Process;
use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Abc\Bundle\SupervisorBundle\Test\CommandTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessStopCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->any())
            ->method('getName')
            ->willReturn('Foobar');

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->once())
            ->method('getProcesses')
            ->willReturn([$process]);

        $supervisor->expects($this->once())
            ->method('stopProcess')
            ->with($process, true);

        $this->manager->expects($this->once())
            ->method('findAll')
            ->willReturn([$supervisor]);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $statusCode = $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertEquals(0, $statusCode);

        $output = $commandTester->getDisplay();
        $this->assertContains('Stopping process Foobar', $output);
    }

    public function testExecuteWithSupervisorThrowsException()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->any())
            ->method('getName')
            ->willReturn('Foobar');

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->once())
            ->method('getProcesses')
            ->willReturn([$process]);

        $supervisor->expects($this->once())
            ->method('stopProcess')
            ->with($process, true)
            ->willThrowException(new \Exception('Supervisor Exception'));

        $this->manager->expects($this->once())
            ->method('findAll')
            ->willReturn([$supervisor]);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $statusCode = $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertEquals(1, $statusCode);

        $output = $commandTester->getDisplay();
        $this->assertContains('Stopping process Foobar', $output);
        $this->assertContains('Supervisor Exception', $output);
    }

    public function testExecuteWithProcessAlreadyStopped()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->any())
            ->method('getName')
            ->willReturn('Foobar');

        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->once())
            ->method('getProcesses')
            ->willReturn([$process]);

        $supervisor->expects($this->once())
            ->method('stopProcess')
            ->with($process, true)
            ->willThrowException(new \Exception('NOT_RUNNING'));

        $this->manager->expects($this->once())
            ->method('findAll')
            ->willReturn([$supervisor]);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $statusCode = $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertEquals(0, $statusCode);

        $output = $commandTester->getDisplay();
        $this->assertContains('Stopping process Foobar', $output);
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

        $supervisor->expects($this->never())
            ->method('stopProcess');

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
     * {@inheritdoc}
     */
    protected function getCommand()
    {
        return new ProcessStopCommand();
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName()
    {
        return 'abc:supervisor:process:stop';
    }
}