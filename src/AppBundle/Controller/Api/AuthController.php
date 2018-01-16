<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\Post;

class AuthController extends AbstractRestController
{
    /**
     * @Post("/api/login_check", name="login_check")
     * @ApiDoc(
     *      resource = true,
     *      description = "logic check",
     *      parameters={
     *          {"name"="_username", "dataType"="string", "required"=true, "description"="username"},
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

}
