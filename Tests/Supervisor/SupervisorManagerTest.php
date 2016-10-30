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

use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;

class SupervisorManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SupervisorManager
     */
    private $subject;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->subject = new SupervisorManager();
    }

    public function testAdd()
    {
        $supervisor = $this->createMock(Supervisor::class);

        $this->subject->add($supervisor);

        $this->assertSame([$supervisor], $this->subject->findAll());
    }

    public function testFindByHost()
    {
        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->any())
            ->method('getHost')
            ->willReturn('foobar');

        $this->subject->add($supervisor);

        $this->assertSame($supervisor, $this->subject->findByHost('foobar'));
        $this->assertEmpty($this->subject->findByHost('undefined'));
    }

    public function testFindById()
    {
        $supervisor = $this->createMock(Supervisor::class);
        $supervisor->expects($this->any())
            ->method('getId')
            ->willReturn('foobar');

        $this->subject->add($supervisor);

        $this->assertSame($supervisor, $this->subject->findById('foobar'));
        $this->assertEmpty($this->subject->findById('undefined'));
    }
}