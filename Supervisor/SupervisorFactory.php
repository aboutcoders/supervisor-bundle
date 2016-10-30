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
     * @param string    $id
     * @param string    $host
     * @param Connector $connector
     * @return Supervisor
     */
    public function createSupervisor($id, $host, Connector $connector)
    {
        return new Supervisor($id, $host, new \Supervisor\Supervisor($connector));
    }
}