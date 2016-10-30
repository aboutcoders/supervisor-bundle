<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Tests\Functional\Controller;

use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;
use Abc\Bundle\SupervisorBundle\Test\WebTestCase;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessControllerTest extends WebTestCase
{
    /**
     * @var SupervisorManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->manager = $this->createMock(SupervisorManager::class);
    }

    public function testListAction()
    {
        $url = '/api/localhost/processes';

        $client = static::createClient();

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->mockServices(['abc.supervisor.manager' => $this->manager]);

        $client->request('GET', $url);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testStartAction()
    {
        $url = '/api/localhost/processes/foobar/start';

        $client = static::createClient();

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->mockServices(['abc.supervisor.manager' => $this->manager]);

        $client->request('POST', $url);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testStopAction()
    {
        $url = '/api/localhost/processes/foobar/stop';

        $client = static::createClient();

        $this->manager->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->mockServices(['abc.supervisor.manager' => $this->manager]);

        $client->request('POST', $url);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}