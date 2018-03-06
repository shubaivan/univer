<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Role;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleController extends AbstractRestController
{
    /**
     * Get role by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/role/{id} <br>.
     *
     * @Rest\Get("/api/admins/role/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get role by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Role"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getAdminRoleAction(Role $role)
    {
        return $this->createSuccessResponse($role, ['get_roles'], true);
    }

    /**
     * Get list roles.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/roles <br>.
     *
     * @Rest\Get("/api/admins/roles")
     * @ApiDoc(
     * resource = true,
     * description = "Get list roles",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Role"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getAdminRolesAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $roleRepository = $em->getRepository('AppBundle:Role');
            $roles = $roleRepository->findAll();

            return $this->createSuccessResponse(
                [
                    'roles' => $roles,
                    'total' => count($roles),
                ],
                ['get_roles'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create role by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/role <br>.
     *
     * @Rest\Post("/api/admins/role")
     * @ApiDoc(
     * resource = true,
     * description = "Create role by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Role"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postAdminRoleAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var Role $role */
            $role = $auth->validateEntites('request', Role::class, ['admin_post_role']);

            $em->persist($role);
            $em->flush();

            return $this->createSuccessResponse($role, ['get_roles'], true);
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
     * Put role by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/role/{id} <br>.
     *
     * @Rest\Put("/api/admins/role/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put role by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Role"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putAdminRoleAction(Request $request, Role $role)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $role->getId());
            /** @var Role $role */
            $role = $auth->validateEntites('request', Role::class, ['admin_put_role']);

            $em->flush();

            return $this->createSuccessResponse($role, ['get_roles'], true);
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
     * Delete Role by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/role/{id} <br>.
     *
     * @Rest\Delete("/api/admins/role/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete Role by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins Role"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedRoleAction(Role $role)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($role);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
