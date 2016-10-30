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

use Doctrine\Common\Collections\ArrayCollection;
use Supervisor\Supervisor as Client;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class Supervisor
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ArrayCollection|Process[]
     */
    protected $processes;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @param string $id
     * @param string $host
     * @param Client $client
     */
    public function __construct($id, $host, Client $client)
    {
        $this->id     = $id;
        $this->host   = $host;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if (null == $this->status) {
            $statusArray  = $this->getClient()->getState();
            $this->status = $statusArray['statename'];
        }

        return $this->status;
    }

    /**
     *
     * @param string|null $group The name of a process group
     * @return ProcessInterface[]
     */
    public function getProcesses($group = null)
    {
        if (!$this->initialized) {
            $this->init();
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

        return $this->processes;
    }

    /**
     * @param string $name The name of the process
     * @return ProcessInterface
     * @throws \InvalidArgumentException If a process with the given name does not exist
     */
    public function getProcess($name)
    {
        if (!$this->initialized) {
            $this->init();
        }

        foreach ($this->processes as $process) {
            if ($name == $process->getName()) {
                return $process;
            }
        }

        throw new \InvalidArgumentException(sprintf('A process with name "%s" does not exist', $name));
    }

    /**
     * @param ProcessInterface $process
     * @param boolean          $wait Whether to wait until process is started (optional, true by default)
     * @throws \InvalidArgumentException If the process is not managed
     */
    public function startProcess(ProcessInterface $process, $wait = true)
    {
        $this->getClient()->startProcess($this->doGetProcess($process)->getId(), $wait);
    }

    /**
     * @param ProcessInterface $process
     * @param boolean          $wait Whether to wait until process is stopped (optional, true by default)
     * @throws \InvalidArgumentException If the process is not managed
     */
    public function stopProcess(ProcessInterface $process, $wait = true)
    {
        $this->getClient()->stopProcess($this->doGetProcess($process)->getId(), $wait);
    }

    /**
     * @param ProcessInterface $process
     * @return void
     * @throws \InvalidArgumentException If the process is not managed
     */
    public function refreshProcess(ProcessInterface $process)
    {
        $process = $this->doGetProcess($process);
        $process->setProcess($this->getClient()->getProcess($process->getId()));
    }

    /**
     * @return void
     */
    protected function init()
    {
        $this->processes = new ArrayCollection();
        foreach ($this->getClient()->getAllProcesses() as $process) {
            $this->processes->add(new Process($process));
        }
        $this->initialized = true;
    }

    /**
     * @param ProcessInterface $process
     * @return Process
     * @throws \InvalidArgumentException If the process is not managed
     */
    protected function doGetProcess(ProcessInterface $process)
    {
        if (!$process instanceof Process || !$this->processes->contains($process)) {
            throw new \InvalidArgumentException('The given instance is not managed by this manager');
        }

        return $process;
    }
}