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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessControllerTest extends WebTestCase
{
    public function testListAction()
    {
        $url = '/api/localhost/processes';

        $client = static::createClient();

        $client->request(
            'GET',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null,
            'json'
        );

        //$this->assertEquals(200, $client->getResponse()->getStatusCode());

        var_dump($client->getResponse()->getContent());
    }

    public function testStopAction()
    {
        $url = '/api/localhost/processes/queue-agent_default/stop';

        $client = static::createClient();

        $client->request(
            'POST',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null,
            'json'
        );

        var_dump($client->getResponse()->getContent());
    }

    public function testStartAction()
    {
        $url = '/api/localhost/processes/queue-agent_default/start';

        $client = static::createClient();

        $client->request(
            'POST',
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null,
            'json'
        );

        //$this->assertEquals(200, $client->getResponse()->getStatusCode());

        var_dump($client->getResponse()->getContent());
    }
}