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

use Supervisor\Supervisor as Client;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class Supervisor
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var Process[]
     */
    protected $processes;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param string $name
     * @param string $host
     * @param Client $client
     */
    public function __construct($name, $host, Client $client)
    {
        $this->name   = $name;
        $this->host   = $host;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function start($name, $wait = true)
    {
        $this->getClient()->startProcess($this->getProcess($name)->getId(), $wait);
    }

    /**
     * {@inheritdoc}
     */
    public function stop($name, $wait = true)
    {
        $this->getClient()->stopProcess($this->getProcess($name)->getId(), $wait);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $name
     * @return Process
     * @throws \InvalidArgumentException If a process with the given name does not exist
     */
    public function getProcess($name)
    {
        if (null == $this->processes) {
            $this->initProcesses();
        }

        if (!isset($this->processes[$name])) {
            throw new \InvalidArgumentException(sprintf('A process with name "%s" does not exist', $name));
        }

        return $this->processes[$name];
    }

    /**
     *
     * @param string|null $group The name of a process group
     * @return Process[]
     */
    public function getProcesses($group = null)
    {
        if (null == $this->processes) {
            $this->initProcesses();
        }

        if (null != $group) {
            $processes = [];
            foreach ($this->processes as $process) {
                if ($group == $process->getGroup()) {
                    $processes[] = $process;
                }
            }

            return $processes;
        }

        return array_values($this->processes);
    }

    public function loadProcess($name)
    {
        return new Process($this->getClient()->getProcess($this->getProcess($name)->getId()));
    }

    protected function initProcesses()
    {
        foreach ($this->getClient()->getAllProcesses() as $process) {
            /**
             * @var \Supervisor\Process $process
             */
            $this->processes[$process->getName()] = new Process($process);
        }
    }
}