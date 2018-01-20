<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends AbstractRestController
{
    /**
     * @Post("/api/login_check", name="login_check")
     * @ApiDoc(
     *      resource = true,
     *      description = "logic check",
     *      parameters={
     *          {"name"="_email", "dataType"="string", "required"=true, "description"="email"},
     *          {"name"="_password", "dataType"="string", "required"=true, "description"="password"}
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Authentification"
     * )
     */
    public function loginAction(Request $request)
    {
    }

    /**
     * @Post("/api/token/refresh", name="api_token_refresh")
     * @ApiDoc(
     *      resource = true,
     *      authentication=true,
     *      description = "logic check",
     *      parameters={
     *          {"name"="refresh_token", "dataType"="string", "required"=true, "description"="refresh_token"}
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Authentification"
     * )
     */
    public function refreshAction(Request $request)
    {
    }
}
