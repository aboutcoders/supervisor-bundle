<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Test;

use Abc\Bundle\SupervisorBundle\Command\BaseCommand;
use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
abstract class CommandTestCase extends KernelTestCase
{
    /**
     * @var SupervisorManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var BaseCommand
     */
    protected $command;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->manager = $this->createMock(SupervisorManager::class);
    }

    public function testExecuteWithInvalidId()
    {
        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--id'    => 'foobar'
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('No supervisor instance configured for id "foobar"', $output);
    }

    public function testExecuteWithInvalidHost()
    {
        $this->manager->expects($this->once())
            ->method('findByHost')
            ->willReturn(null);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--host'  => 'foobar'
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('No supervisor instance configured for host "foobar"', $output);
    }

    public function testExecuteWithIdAndHostOption()
    {
        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--id'    => 'foobar',
            '--host'  => 'foobar'
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('You can either specify the option "id" or the option "host"', $output);
    }

    public function testExecuteWithNoSupervisors()
    {
        $this->manager->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $application = $this->setUpApplication($this->manager);

        $command       = $application->find($this->getCommandName());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('No supervisor instance configured', $output);
    }

    /**
     * @param SupervisorManager $manager
     * @return Application
     */
    protected function setUpApplication(SupervisorManager $manager)
    {
        self::bootKernel();

        $application = new Application(self::$kernel);
        $application->add($this->getCommand());

        static::$kernel->getContainer()->set('abc.supervisor.manager', $manager);

        return $application;
    }

    /**
     * @return BaseCommand
     */
    protected abstract function getCommand();

    /**
     * @return string
     */
    protected abstract function getCommandName();
}