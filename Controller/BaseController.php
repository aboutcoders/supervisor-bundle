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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
abstract class BaseController extends Controller
{
    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @ToDo To be removed when dependency on Symfony 2.7 is bumped
     * @param mixed $data    The response data
     * @param int   $status  The status code to use for the Response
     * @param array $headers Array of extra headers to add
     * @param array $context
     * @return JsonResponse
     */
    protected function json($data, $status = 200, $headers = array(), $context = array())
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @return SupervisorManager
     */
    protected function getManager()
    {
        return $this->get('abc.supervisor.manager');
    }
}