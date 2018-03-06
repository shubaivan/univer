<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Semesters;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SemestersController extends AbstractRestController
{
    /**
     * Get semester by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/semester/{id} <br>.
     *
     * @Rest\Get("/api/semester/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get semester by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Semester"
     * )
     *
     * @RestView()
     *
     * @param Semesters $semesters
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getSemestersAction(Semesters $semesters)
    {
        return $this->createSuccessResponse($semesters, ['get_semester'], true);
    }

    /**
     * Get list semesters.
     * <strong>Simple example:</strong><br />
     * http://host/api/semesters <br>.
     *
     * @Rest\Get("/api/semesters")
     * @ApiDoc(
     * resource = true,
     * description = "Get list semesters",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Semester"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search fields - name")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getAdminSemesterAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $semesters = $em->getRepository('AppBundle:Semesters');

            return $this->createSuccessResponse(
                [
                    'semesters' => $semesters->getEntitiesByParams($paramFetcher),
                    'total' => $semesters->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_semesters'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create semester by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/semester <br>.
     *
     * @Rest\Post("/api/admins/semester")
     * @ApiDoc(
     * resource = true,
     * description = "Create semester by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Semester"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postAdminSemestersAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var Semesters $semesters */
            $semesters = $auth->validateEntites('request', Semesters::class, ['post_semester']);

            $em->persist($semesters);
            $em->flush();

            return $this->createSuccessResponse($semesters, ['get_semester'], true);
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
     * Put semester by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/semester/{id} <br>.
     *
     * @Rest\Put("/api/admins/semester/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put semester by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Semester"
     * )
     *
     * @RestView()
     *
     * @param Request   $request
     * @param Semesters $semesters
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putAdminSemestersAction(Request $request, Semesters $semesters)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $semesters->getId());
            /** @var Semesters $semesters */
            $semesters = $auth->validateEntites('request', Semesters::class, ['put_semester']);

            $em->flush();

            return $this->createSuccessResponse($semesters, ['get_semester'], true);
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
     * Delete semester by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/semester/{id} <br>.
     *
     * @Rest\Delete("/api/admins/semester/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete semester by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins Semester"
     * )
     *
     * @RestView()
     *
     * @param Semesters $semesters
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedSemestersAction(Semesters $semesters)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($semesters);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
