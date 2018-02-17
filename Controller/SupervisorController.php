<?php
/*
* This file is part of the job-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorController extends BaseController
{
    /**
     * Returns a list of supervisors.
     *
     * @Operation(
     *     tags={"AbcSupervisorBundle"},
     *     summary="Returns a collection of supervisors",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when supervisor not found"
     *     )
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction()
    {
        $supervisors = array();
        foreach ($this->getManager()->findAll() as $supervisor) {
            $data                = array();
            $data['id']          = $supervisor->getId();
            $data['host']        = $supervisor->getHost();
            $data['pid']         = $supervisor->getClient()->getPID();
            $data['status']      = $supervisor->getStatus();
            $data['api_version'] = $supervisor->getClient()->getAPIVersion();
            $supervisors[]       = $data;
        }

        return $this->json($supervisors);
    }
}
