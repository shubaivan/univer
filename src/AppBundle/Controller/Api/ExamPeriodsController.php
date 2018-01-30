<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\ExamPeriods;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExamPeriodsController extends AbstractRestController
{
    /**
     * Get exam period by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/exam_period/{id} <br>.
     *
     * @Rest\Get("/api/exam_period/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get exam period by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins ExamPeriod"
     * )
     *
     * @RestView()
     *
     * @param ExamPeriods $examPeriods
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getExamPeriodAction(ExamPeriods $examPeriods)
    {
        return $this->createSuccessResponse($examPeriods, ['get_exam_period'], true);
    }

    /**
     * Get list exam periods.
     * <strong>Simple example:</strong><br />
     * http://host/api/exam_periods <br>.
     *
     * @Rest\Get("/api/exam_periods")
     * @ApiDoc(
     * resource = true,
     * description = "Get list exam periods",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins ExamPeriod"
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
    public function getAdminExamPeriodAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $examPeriods = $em->getRepository('AppBundle:ExamPeriods');

            return $this->createSuccessResponse(
                [
                    'exam_periods' => $examPeriods->getEntitiesByParams($paramFetcher),
                    'total' => $examPeriods->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_exam_periods'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create exam period by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/exam_period <br>.
     *
     * @Rest\Post("/api/admins/exam_period")
     * @ApiDoc(
     * resource = true,
     * description = "Create exam period by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins ExamPeriod"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postAdminExamPeriodAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var ExamPeriods $examPeriods */
            $examPeriods = $auth->validateEntites('request', ExamPeriods::class, ['post_exam_period']);

            $em->persist($examPeriods);
            $em->flush();

            return $this->createSuccessResponse($examPeriods, ['get_exam_period'], true);
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
     * Put exam period by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/exam_period/{id} <br>.
     *
     * @Rest\Put("/api/admins/exam_period/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put exam period by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="name", "dataType"="string", "required"=true, "description"="name"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Admins ExamPeriod"
     * )
     *
     * @RestView()
     *
     * @param Request     $request
     * @param ExamPeriods $examPeriods
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putAdminExamPeriodAction(Request $request, ExamPeriods $examPeriods)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $examPeriods->getId());
            /** @var ExamPeriods $examPeriods */
            $examPeriods = $auth->validateEntites('request', ExamPeriods::class, ['put_exam_period']);

            $em->flush();

            return $this->createSuccessResponse($examPeriods, ['get_exam_period'], true);
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
     * Delete exam period by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/exam_period/{id} <br>.
     *
     * @Rest\Delete("/api/admins/exam_period/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete exam period by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins ExamPeriod"
     * )
     *
     * @RestView()
     *
     * @param ExamPeriods $examPeriods
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedExamPeriodAction(ExamPeriods $examPeriods)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($examPeriods);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
