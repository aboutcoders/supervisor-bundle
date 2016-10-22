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

use Supervisor\Process as BaseProcess;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class Process
{
    /**
     * @var BaseProcess
     */
    protected $process;

    /**
     * @param BaseProcess $process
     */
    public function __construct(BaseProcess $process)
    {
        $this->process = $process;
    }

    public function getId()
    {
        return $this->getGroup() == null ? $this->getName() : $this->getGroup() . ':' . $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->process->getName();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->process->offsetGet('description');
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->process->offsetGet('group');
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->process->offsetGet('pid');
    }

    /**
     * @return int
     */
    public function getUptime()
    {
        return $this->process->offsetGet('uptime');
    }

    /**
     * @return string
     */
    public function getLogfile()
    {
        return $this->process->offsetGet('logfile');
    }

    /**
     * @return string
     */
    public function getStderrLogfile()
    {
        return $this->process->offsetGet('stderr_logfile');
    }

    /**
     * @return string
     */
    public function getStdoutLogfile()
    {
        return $this->process->offsetGet('stdout_logfile');
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->process->offsetGet('state');
    }

    /**
     * @return string
     */
    public function getStatename()
    {
        return $this->process->offsetGet('statename');
    }

    /**
     * @return string
     */
    public function getStart()
    {
        return $this->process->offsetGet('start');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->process->getPayload();
    }
}