<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Model\Request\RecoveryPasswordRequest;
use AppBundle\Security\AuthenticationSuccessHandler;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
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
     * http://host/api/registration <br>.
     *
     * @ApiDoc(
     * resource = true,
     * description = "User Registration",
     *  parameters={
     *      {"name"="_password", "dataType"="string", "required"=true, "description"="user password"},
     *      {"name"="_email", "dataType"="string", "required"=true, "description"="user email"},
     *      {"name"="_username", "dataType"="string", "required"=true, "description"="user name"}
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
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        $encoder = $this->container->get('security.password_encoder');
        $logger = $this->container->get('logger');

        try {
            $role = $em->getRepository('AppBundle\Entity\Role')
                ->findOneBy(['name' => User::ROLE_USER]);
            if (!$role) {
                $role = new Role();
                $role->setName(User::ROLE_USER);
                $em->persist($role);
            }

            $auth = $this->get('app.auth');

            /** @var User $user */
            $user = $auth->validateEntites('request', User::class, ['registration']);
            $password = $request->request->get('_password');

            $user
                ->addUserRole($role)
                ->setPassword($encoder->encodePassword($user, $password));
            $em->persist($user);
            $em->flush();
            /** @var AuthenticationSuccessHandler $lexikJwtAuthentication */
            $lexikJwtAuthentication = $this->get('custom');
            $event = $lexikJwtAuthentication->handleAuthenticationSuccess($user, null, true);

            return $this->createSuccessResponse($event->getData());
        } catch (ValidatorException $e) {
            $view = $this->view($e->getConstraintViolatinosList(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Recovery User password.
     * <strong>Simple example:</strong><br />
     * http://bbt.dev/api/recoveries/passwords <br>.
     *
     * @Rest\Post("/recoveries/passwords", name="recoveries_passwords")
     * @ApiDoc(
     * resource = true,
     * description = "Recovery User password",
     * authentication=true,
     *  parameters={
     *      {"name"="old_password", "dataType"="string", "required"=false, "description"="user old password"},
     *      {"name"="new_password", "dataType"="string", "required"=false, "description"="user new password"},
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
     * @param Request $request
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postRecoveryPasswordAction(Request $request)
    {
        $logger = $this->container->get('logger');

        try {
            /** @var RecoveryPasswordRequest|View $validateRequest */
            $validateRequest = $this->validateEntites(
                $request,
                'request',
                RecoveryPasswordRequest::class
            );

            /** @var User $user */
            $user = $this->getUser();
            $encoderFactory = $this->container->get('security.encoder_factory');

            if (!$encoderFactory->getEncoder($user)
                ->isPasswordValid($user->getPassword(), $validateRequest->getOldPassword(), $user->getSalt())
            ) {
                $view = $this->view(
                    [
                        'property_path' => 'old_password',
                        'message' => 'current password invalid',
                    ],
                    self::HTTP_STATUS_CODE_BAD_REQUEST
                );

                return $this->handleView($view);
            }
            $encoder = $this->container->get('security.password_encoder');
            $user
                ->setPassword($encoder->encodePassword($user, $validateRequest->getNewPassword()));
            /** @var EntityManager $em */
            $em = $this->get('doctrine')->getManager();
            $em->flush();
            $message = \Swift_Message::newInstance();
            $root = $this->get('kernel')->getRootDir();
            $imgUrl = $message->embed(
                \Swift_Image::fromPath(
                $root.'/../web/bundles/app/img/password-recovery-app-256.png'
            )
            );
            $message
                ->setFrom('admin@univer.com', 'Klaizar')
                ->setSubject('recovery password')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        ':mail:recovery.html.twig',
                        [
                            'user' => $user,
                            'new_password' => $validateRequest->getNewPassword(),
                            'img' => $imgUrl,
                        ]
                    ),
                    'text/html'
                );

            $mailer = $this->get('mailer');
            $mailer->send($message);

            return $this->createSuccessStringResponse('success');
        } catch (ValidatorException $e) {
            $view = $this->view($e->getConstraintViolatinosList(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
