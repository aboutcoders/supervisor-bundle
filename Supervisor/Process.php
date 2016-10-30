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
class Process implements ProcessInterface
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

    /**
     * @param BaseProcess $process
     * @return void
     */
    public function setProcess(BaseProcess $process) {
        $this->process = $process;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getGroup() == null ? $this->getName() : $this->getGroup() . ':' . $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->process->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->process->offsetGet('description');
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return $this->process->offsetGet('group');
    }

    /**
     * {@inheritdoc}
     */
    public function getPid()
    {
        return $this->process->offsetGet('pid');
    }

    /**
     * {@inheritdoc}
     */
    public function getUptime()
    {
        return $this->process->offsetGet('uptime');
    }

    /**
     * {@inheritdoc}
     */
    public function getLogfile()
    {
        return $this->process->offsetGet('logfile');
    }

    /**
     * {@inheritdoc}
     */
    public function getStderrLogfile()
    {
        return $this->process->offsetGet('stderr_logfile');
    }

    /**
     * {@inheritdoc}
     */
    public function getStdoutLogfile()
    {
        return $this->process->offsetGet('stdout_logfile');
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->process->offsetGet('state');
    }

    /**
     * {@inheritdoc}
     */
    public function getStatename()
    {
        return $this->process->offsetGet('statename');
    }

    /**
     * {@inheritdoc}
     */
    public function getStart()
    {
        return $this->process->offsetGet('start');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->process->getPayload();
    }
}