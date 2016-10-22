<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Supervisor;

use fXmlRpc\Client;
use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Supervisor\Connector\XmlRpc;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ConnectorFactory
{
    /**
     * @param $config
     * @return XmlRpc
     */
    public function createConnector($config)
    {
        $clientConfig = [];
        if (isset($config['username'])) {
            $clientConfig = ['auth' => [$config['username'], isset($config['password']) ? $config['password'] : '']];
        }

        $guzzleHttpClient = new \GuzzleHttp\Client($clientConfig);
        $httpClient = new \Http\Adapter\Guzzle6\Client($guzzleHttpClient);

        $client = new Client(
            $config['host'] . ':' . $config['port'] . '/RPC2',
            new HttpAdapterTransport(new GuzzleMessageFactory(), $httpClient)
        );

        return new XmlRpc($client);
    }
}