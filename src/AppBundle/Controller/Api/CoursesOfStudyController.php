<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\CoursesOfStudy;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CoursesOfStudyController extends AbstractRestController
{
    /**
     * Get course of study by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/course_of_study/{id} <br>.
     *
     * @Rest\Get("/api/course_of_study/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get course of study by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="CoursesOfStudy"
     * )
     *
     * @RestView()
     *
     * @param CoursesOfStudy $coursesOfStudy
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getCourseOfStudyAction(CoursesOfStudy $coursesOfStudy)
    {
        return $this->createSuccessResponse($coursesOfStudy, ['get_course_of_study'], true);
    }

    /**
     * Get list courses of study.
     * <strong>Simple example:</strong><br />
     * http://host/api/courses_of_study <br>.
     *
     * @Rest\Get("/api/courses_of_study")
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
     * section="CoursesOfStudy"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search field")
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
    public function getAdminCoursesOfStudyAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $coursesOfStudy = $em->getRepository('AppBundle:CoursesOfStudy');

            return $this->createSuccessResponse(
                [
                    'courses_of_study' => $coursesOfStudy->getEntitiesByParams($paramFetcher),
                    'total' => $coursesOfStudy->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_courses_of_study'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create course of study by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/course_of_study <br>.
     *
     * @Rest\Post("/api/admins/course_of_study")
     * @ApiDoc(
     * resource = true,
     * description = "Create course of study by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins CoursesOfStudy"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postAdminCourseOfStudyAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var CoursesOfStudy $coursesOfStudy */
            $coursesOfStudy = $auth->validateEntites('request', CoursesOfStudy::class, ['post_course_of_study']);

            $em->persist($coursesOfStudy);
            $em->flush();

            return $this->createSuccessResponse($coursesOfStudy, ['get_course_of_study'], true);
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
     * Put course of study by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/course_of_study/{id} <br>.
     *
     * @Rest\Put("/api/admins/course_of_study/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put course of study by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins CoursesOfStudy"
     * )
     *
     * @RestView()
     *
     * @param Request $request
     * @param CoursesOfStudy $coursesOfStudy
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putAdminCourseOfStudyAction(Request $request, CoursesOfStudy $coursesOfStudy)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $coursesOfStudy->getId());
            /** @var CoursesOfStudy $coursesOfStudy */
            $coursesOfStudy = $auth->validateEntites('request', CoursesOfStudy::class, ['put_course_of_study']);

            $em->flush();

            return $this->createSuccessResponse($coursesOfStudy, ['get_course_of_study'], true);
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
     * Delete course of study by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/course_of_study/{id} <br>.
     *
     * @Rest\Delete("/api/admins/course_of_study/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete course of study by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins CoursesOfStudy"
     * )
     *
     * @RestView()
     *
     * @param CoursesOfStudy $coursesOfStudy
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedCourseOfStudyAction(CoursesOfStudy $coursesOfStudy)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($coursesOfStudy);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
