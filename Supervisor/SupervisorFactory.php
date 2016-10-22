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

use Supervisor\Connector;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorFactory
{
    /**
     * @param           $name
     * @param           $host
     * @param Connector $connector
     * @return Supervisor
     */
    public function createSupervisor($name, $host, Connector $connector)
    {
        return new Supervisor($name, $host, new \Supervisor\Supervisor($connector));
    }
}