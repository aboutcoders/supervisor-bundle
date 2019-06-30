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
     * @return SupervisorManager
     */
    protected function getManager()
    {
        return $this->get('abc.supervisor.manager');
    }
}
