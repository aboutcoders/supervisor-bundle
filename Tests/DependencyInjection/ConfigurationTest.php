<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Tests\DependencyInjection;

use Abc\Bundle\SupervisorBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(true), array(array('connections' => ['foobar' => [
            'host' => 'HostName',
            'port' => 9000,
        ]])));

        $this->assertEquals(
            array('connections' => ['foobar' => ['host' => 'HostName', 'port' => 9000]]),
            $config
        );
    }
}