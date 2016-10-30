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
interface ProcessInterface
{
    /**
     * @return string The unique process id
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getGroup();

    /**
     * @return int
     */
    public function getPid();

    /**
     * @return int The processing time in seconds
     */
    public function getUptime();

    /**
     * @return string
     */
    public function getLogfile();

    /**
     * @return string
     */
    public function getStderrLogfile();

    /**
     * @return string
     */
    public function getStdoutLogfile();

    /**
     * @return int The status code
     */
    public function getState();

    /**
     * @return string The status name
     */
    public function getStatename();

    /**
     * @return int The unix timestamp when the process was started
     */
    public function getStart();

    /**
     * @return array
     */
    public function toArray();
}