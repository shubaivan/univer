<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Courses;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CoursesController extends AbstractRestController
{
    /**
     * Get course  by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/course/{id} <br>.
     *
     * @Rest\Get("/api/course/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get course  by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Courses"
     * )
     *
     * @RestView()
     *
     * @param Courses $courses
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getCourseAction(Courses $courses)
    {
        return $this->createSuccessResponse($courses, ['get_course'], true);
    }

    /**
     * Get list courses .
     * <strong>Simple example:</strong><br />
     * http://host/api/courses <br>.
     *
     * @Rest\Get("/api/courses")
     * @ApiDoc(
     * resource = true,
     * description = "Get list courses ",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Courses"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search field")
     * @Rest\QueryParam(name="courses_of_study", requirements="\d+", description="courses_of_study id")
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
    public function getAdminCoursesAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $courses = $em->getRepository('AppBundle:Courses');

            return $this->createSuccessResponse(
                [
                    'courses' => $courses->getEntitiesByParams($paramFetcher),
                    'total' => $courses->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_courses'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create course  by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/course <br>.
     *
     * @Rest\Post("/api/admins/course")
     * @ApiDoc(
     * resource = true,
     * description = "Create course  by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"},
     *      {"name"="courses_of_study", "dataType"="string", "required"=false, "description"="courses of study id"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Courses"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postAdminCourseAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var Courses $course */
            $course = $auth->validateEntites('request', Courses::class, ['post_course']);

            $em->persist($course);
            $em->flush();

            return $this->createSuccessResponse($course, ['get_course'], true);
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
     * Put course  by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/course/{id} <br>.
     *
     * @Rest\Put("/api/admins/course/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put course  by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"},
     *      {"name"="courses_of_study", "dataType"="string", "required"=false, "description"="courses of study id"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins Courses"
     * )
     *
     * @RestView()
     *
     * @param Request $request
     * @param Courses $courses
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putAdminCourseAction(Request $request, Courses $courses)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $courses->getId());
            /** @var Courses $courses */
            $courses = $auth->validateEntites('request', Courses::class, ['put_course']);

            $em->flush();

            return $this->createSuccessResponse($courses, ['get_course'], true);
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
     * Delete course  by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/course/{id} <br>.
     *
     * @Rest\Delete("/api/admins/course/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete course  by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins Courses"
     * )
     *
     * @RestView()
     *
     * @param Courses $courses
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedCourseAction(Courses $courses)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($courses);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
