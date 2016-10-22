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
     * @param string $key The supervisor key
     * @return JsonResponse
     */
    public function listAction($key)
    {
        $supervisor = $this->getManager()->findByKey($key);
        if (!$supervisor) {
            return $this->json(['error' => sprintf('Supervisor with key "%s" not found', $key)], 404);
        }

        return $this->json($supervisor->getClient()->getAllProcessInfo());
    }

    /**
     * Starts a process.
     *
     * @param string $key  The supervisor key
     * @param string $name The process name
     * @param bool   $wait
     * @return JsonResponse
     */
    public function startAction($key, $name, $wait = false)
    {
        $supervisor = $this->getManager()->findByKey($key);
        if (!$supervisor) {
            return $this->json(['error' => sprintf('Supervisor with key "%s" not found', $key)], 404);
        }

        try {
            $supervisor->start($name, $wait);

            return $this->json($supervisor->loadProcess($name)->toArray());
        } catch (FaultException $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Stops a process.
     *
     * @param string $key  The supervisor key
     * @param string $name The process name
     * @param bool   $wait
     * @return JsonResponse
     */
    public function stopAction($key, $name, $wait = false)
    {
        $supervisor = $this->getManager()->findByKey($key);
        if (!$supervisor) {
            return $this->json(['error' => sprintf('Supervisor with key "%s" not found', $key)], 404);
        }

        try {
            $supervisor->stop($name, $wait);

            return $this->json($supervisor->loadProcess($name)->toArray());
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