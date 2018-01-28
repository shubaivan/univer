<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\SubCourses;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubCoursesController extends AbstractRestController
{
    /**
     * Get sub course by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/sub_course/{id} <br>.
     *
     * @Rest\Get("/api/admins/sub_course/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get sub course by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins SubCourse"
     * )
     *
     * @RestView()
     *
     * @param SubCourses $subCourses
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getSubCourseAction(SubCourses $subCourses)
    {
        return $this->createSuccessResponse($subCourses, ['get_sub_course'], true);
    }

    /**
     * Get list sub courses of study.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/sub_courses <br>.
     *
     * @Rest\Get("/api/admins/sub_courses")
     * @ApiDoc(
     * resource = true,
     * description = "Get list courses of study",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins SubCourse"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search field")
     * @Rest\QueryParam(name="courses", requirements="\d+", description="courses id")
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
    public function getAdminSubCourseAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $subCourses = $em->getRepository('AppBundle:SubCourses');

            return $this->createSuccessResponse(
                [
                    'sub_courses' => $subCourses->getEntitiesByParams($paramFetcher),
                    'total' => $subCourses->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_sub_courses'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create sub course by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/sub_course <br>.
     *
     * @Rest\Post("/api/admins/sub_course")
     * @ApiDoc(
     * resource = true,
     * description = "Create sub course by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"},
     *      {"name"="courses", "dataType"="string", "required"=false, "description"="course id"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins SubCourse"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postAdminSubCourseAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var SubCourses $subCourses */
            $subCourses = $auth->validateEntites('request', SubCourses::class, ['post_sub_course']);

            $em->persist($subCourses);
            $em->flush();

            return $this->createSuccessResponse($subCourses, ['get_sub_courses'], true);
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Put sub course by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/sub_course/{id} <br>.
     *
     * @Rest\Put("/api/admins/sub_course/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put sub course by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"},
     *      {"name"="courses", "dataType"="string", "required"=false, "description"="course id"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins SubCourse"
     * )
     *
     * @RestView()
     *
     * @param Request $request
     * @param SubCourses $subCourses
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putAdminSubCourseAction(Request $request, SubCourses $subCourses)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $subCourses->getId());
            /** @var SubCourses $subCourses */
            $subCourses = $auth->validateEntites('request', SubCourses::class, ['put_sub_course']);

            $em->flush();

            return $this->createSuccessResponse($subCourses, ['get_sub_course'], true);
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Delete sub course by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/sub_course/{id} <br>.
     *
     * @Rest\Delete("/api/admins/sub_course/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete sub course by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins SubCourse"
     * )
     *
     * @RestView()
     *
     * @param SubCourses $subCourses
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedSubCourseAction(SubCourses $subCourses)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($subCourses);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
