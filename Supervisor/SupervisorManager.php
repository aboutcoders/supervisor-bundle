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

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorManager
{
    /**
     * @var Supervisor[]
     */
    protected $supervisors;

    /**
     * @param Supervisor $supervisor
     * @return void
     */
    public function add(Supervisor $supervisor)
    {
        $this->supervisors[] = $supervisor;
    }

    /**
     * @return Supervisor[]
     */
    public function findAll()
    {
        return $this->supervisors;
    }

    /**
     * @param string $key The connection key
     * @return Supervisor|null
     */
    public function findByKey($key)
    {
        foreach ($this->supervisors as $instance) {
            if ($key == $instance->getName()) {
                return $instance;
            }
        }

        return null;
    }

    /**
     * @param string $host
     * @return Supervisor|null
     */
    public function findByHost($host)
    {
        foreach ($this->supervisors as $instance) {
            if ($host == $instance->getHost()) {
                return $instance;
            }
        }

        return null;
    }
}