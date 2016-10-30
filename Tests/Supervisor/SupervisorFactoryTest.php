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
use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorFactory;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSupervisor()
    {
        $subject    = new SupervisorFactory();
        $supervisor = $subject->createSupervisor('Id', 'Host', $this->createMock(\Supervisor\Connector::class));

        $this->assertInstanceOf(Supervisor::class, $supervisor);
    }
}