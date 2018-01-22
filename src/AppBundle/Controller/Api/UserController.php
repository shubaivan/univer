<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends AbstractRestController
{
    /**
     * Get Auth User Profile data.
     * <strong>Simple example:</strong><br />
     * http://bbt.dev/api/user <br>.
     *
     * @Get("/api/user", name="get_user")
     * @ApiDoc(
     * resource = true,
     * description = "Get Auth User Profile data",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="User"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getUserAction()
    {
        return $this->createSuccessResponse($this->getUser(), ['profile'], true);
    }

    /**
     * Get Auth User Profile data.
     * <strong>Simple example:</strong><br />
     * http://bbt.dev/api/admins/user <br>.
     *
     * @Get("/api/admins/user", name="get_admin_user")
     * @ApiDoc(
     * resource = true,
     * description = "Get Auth User Profile data",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="User"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getTestAction()
    {
        return $this->createSuccessResponse($this->getUser(), ['profile'], true);
    }
}
