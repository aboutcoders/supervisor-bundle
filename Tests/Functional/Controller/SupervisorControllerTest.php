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
class SupervisorControllerTest extends WebTestCase
{
    /**
     * @var SupervisorManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $supervisorManager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->supervisorManager = $this->createMock(SupervisorManager::class);
    }

    public function testListAction()
    {
        $url = '/api/localhost';

        $client = static::createClient();

        $this->mockServices(['abc.supervisor.manager' => $this->supervisorManager]);

        $this->supervisorManager->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $client->request(
            'GET',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null,
            'json'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}