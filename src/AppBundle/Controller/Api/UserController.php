<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
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
     * http://host/api/user <br>.
     *
     * @Rest\Get("/api/user", name="get_user")
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
     * Create User by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/users <br>.
     *
     * @Rest\Post("/api/admins/users", name="admin_post_user")
     * @ApiDoc(
     * resource = true,
     * description = "Create User by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="_username", "dataType"="string", "required"=false, "description"="username"},
     *      {"name"="is_active", "dataType"="boolean", "required"=false, "description"="user is_active parameter"},
     *      {"name"="_email", "dataType"="string", "required"=false, "description"="user email"},
     *      {"name"="_password", "dataType"="string", "required"=false, "description"="user password"},
     *      {"name"="first_name", "dataType"="string", "required"=false, "description"="user first_name"},
     *      {"name"="last_name", "dataType"="string", "required"=false, "description"="user last_name"},
     *      {"name"="student_id", "dataType"="integer", "required"=false, "description"="user student_id"},
     *      {"name"="year_of_graduation", "dataType"="integer", "required"=false, "description"="user year_of_graduation"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins"
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
    public function postAdminUserAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $encoder = $this->container->get('security.password_encoder');
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var User $user */
            $user = $auth->validateEntites('request', User::class, ['admin_post_user']);
            $password = $request->request->get('_password');

            $user
                ->setPassword($encoder->encodePassword($user, $password));

            if (!$user->getUserRoles()) {
                $role = $em->getRepository('AppBundle\Entity\Role')
                    ->findOneBy(['name' => User::ROLE_USER]);
                if (!$role) {
                    $role = new Role();
                    $role->setName(User::ROLE_USER);
                    $em->persist($role);
                }

                $user
                    ->addUserRole($role);
            }

            $em->persist($user);
            $em->flush();

            return $this->createSuccessResponse($user, ['profile'], true);
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

//    /**
//     * Get list users for admin.
//     * <strong>Simple example:</strong><br />
//     * http://bbt.dev/admins/users <br>.
//     *
//     * @ApiDoc(
//     * resource = true,
//     * description = "Get list users for admin",
//     *  parameters={
//     *
//     *  },
//     * statusCodes = {
//     *      200 = "Returned when successful",
//     *      400 = "Bad request"
//     * },
//     * section="User"
//     * )
//     *
//     * @RestView()
//     *
//     * @QueryParam(name="search", description="search field")
//     * @QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
//     * @QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
//     * @QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
//     * @QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
//     *
//     * @param Request      $request
//     * @param ParamFetcher $paramFetcher
//     *
//     * @throws NotFoundHttpException when not exist
//     *
//     * @return Response|View
//     */
//    public function getAdminsUserAction(Request $request, ParamFetcher $paramFetcher)
//    {
//        try {
//            $em = $this->getDoctrine()->getManager();
//
//            $userRepository = $em->getRepository('AppBundle:User');
//            $users = $userRepository->getUsersByParams($paramFetcher);
//
//            return $this->createSuccessResponse($users, ['profile'], true);
//        } catch (\Exception $e) {
//            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
//            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
//        }
//
//        return $this->handleView($view);
//    }
//
//    /**
//     * Put User by auth user.
//     * <strong>Simple example:</strong><br />
//     * http://bbt.dev/api/user <br>.
//     *
//     * @Put("/api/user", name="put_user")
//     * @ApiDoc(
//     * resource = true,
//     * description = "Put User by auth user",
//     *  parameters={
//     *      {"name"="first_name", "dataType"="string", "required"=false, "description"="user first_name"},
//     *      {"name"="last_name", "dataType"="string", "required"=false, "description"="user last_name"},
//     *      {"name"="location", "dataType"="string", "required"=false, "description"="user location"},
//     *      {"name"="username", "dataType"="string", "required"=false, "description"="user username"},
//     *      {"name"="_email", "dataType"="string", "required"=false, "description"="user email"}
//     *  },
//     * statusCodes = {
//     *      200 = "Returned when successful",
//     *      400 = "Bad request"
//     * },
//     * section="User"
//     * )
//     *
//     * @RestView()
//     *
//     * @param Request $request
//     *
//     * @throws NotFoundHttpException when not exist
//     *
//     * @return Response|View
//     */
//    public function putUserAction(Request $request)
//    {
//        $em = $this->get('doctrine')->getManager();
//        $logger = $this->container->get('logger');
//
//        try {
//            $auth = $this->get('app.auth');
//            /** @var User $authUser */
//            $authUser = $this->getUser();
//            $request->request->set('id', $authUser->getId());
//
//            /** @var User $user */
//            $user = $auth->validateEntites('request', User::class, ['put_user']);
//
//            $em->flush();
//
//            return $this->createSuccessResponse($user, ['profile'], true);
//        } catch (ValidatorException $e) {
//            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
//            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
//        } catch (\Exception $e) {
//            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
//            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
//        }
//
//        return $this->handleView($view);
//    }
}
