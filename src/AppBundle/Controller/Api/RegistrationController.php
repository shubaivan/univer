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

class RegistrationController extends AbstractRestController
{
    /**
     * User Registration.
     * <strong>Simple example:</strong><br />
     * http://bbt.dev/api/registration <br>.
     *
     * @ApiDoc(
     * resource = true,
     * description = "User Registration",
     *  parameters={
     *      {"name"="_password", "dataType"="string", "required"=false, "description"="user password"},
     *      {"name"="_email", "dataType"="string", "required"=false, "description"="user email"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Registration"
     * )
     *
     * @RestView()
     *
     * @param Request $request
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postRegistrationAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $encoder = $this->container->get('security.password_encoder');
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var User $user */
            $user = $auth->validateEntites('request', User::class, ['registration']);
            $password = $request->request->get('_password');

            $user
                ->setPassword($encoder->encodePassword($user, $password));

            $em->persist($user);
            $em->flush();

            return $this->createSuccessResponse(
                [
                    sprintf('User %s successfully created. Id %s', $user->getUsername(), $user->getId()),
                ]
            );
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
