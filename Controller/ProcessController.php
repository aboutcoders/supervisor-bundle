<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Controller;

use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;
use fXmlRpc\Exception\FaultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessController extends Controller
{
    /**
     * Returns a list of processes.
     *
     * @param string $id The id of the supervisor instance
     * @return JsonResponse
     */
    public function listAction($id)
    {
        $supervisor = $this->getManager()->findById($id);
        if (!$supervisor) {
            return $this->json(['error' => sprintf('Supervisor with id "%s" not found', $id)], 404);
        }

        return $this->json($supervisor->getClient()->getAllProcessInfo());
    }

    /**
     * Starts a process.
     *
     * @param string $id   The id of the supervisor instance
     * @param string $name The process name
     * @param bool   $wait
     * @return JsonResponse
     */
    public function startAction($id, $name, $wait = false)
    {
        $supervisor = $this->getManager()->findById($id);
        if (!$supervisor) {
            return $this->json(['error' => sprintf('Supervisor with id "%s" not found', $id)], 404);
        }

        try {
            $process = $supervisor->getProcess($name);

            $supervisor->startProcess($process, $wait);
            $supervisor->refreshProcess($process);

            return $this->json($process->toArray());
        } catch (FaultException $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Stops a process.
     *
     * @param string $id   The id of the supervisor instance
     * @param string $name The process name
     * @param bool   $wait
     * @return JsonResponse
     */
    public function stopAction($id, $name, $wait = false)
    {
        $supervisor = $this->getManager()->findById($id);
        if (!$supervisor) {
            return $this->json(['error' => sprintf('Supervisor with id "%s" not found', $id)], 404);
        }

        try {
            $process = $supervisor->getProcess($name);

            $supervisor->stopProcess($process, $wait);
            $supervisor->refreshProcess($process);

            return $this->json($process->toArray());
        } catch (FaultException $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @return SupervisorManager
     */
    protected function getManager()
    {
        return $this->get('abc.supervisor.manager');
    }
}