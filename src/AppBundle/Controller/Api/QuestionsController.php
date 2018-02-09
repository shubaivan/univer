<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Questions;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Helper\FileUploader;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuestionsController extends AbstractRestController
{
    /**
     * Get question by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/question/{id} <br>.
     *
     * @Rest\Get("/api/question/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get question by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question"
     * )
     *
     * @RestView()
     *
     * @param Questions $questions
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getQuestionsAction(Questions $questions)
    {
        return $this->createSuccessResponse(
            $questions,
            ['get_question', 'get_user_question_answer_test'],
            true
        );
    }

    /**
     * Get list questions.
     * <strong>Simple example:</strong><br />
     * http://host/api/questions <br>.
     *
     * @Rest\Get("/api/questions")
     * @ApiDoc(
     * resource = true,
     * description = "Get list questions",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search fields - text, notes")
     * @Rest\QueryParam(name="user", requirements="\d+", description="user id")
     * @Rest\QueryParam(name="semesters", requirements="\d+", description="semesters id")
     * @Rest\QueryParam(name="exam_periods", requirements="\d+", description="exam_periods id")
     * @Rest\QueryParam(name="sub_courses", requirements="\d+", description="sub_courses id")
     * @Rest\QueryParam(name="lectors", requirements="\d+", description="lectors id")
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
    public function getAdminQuestionAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine();

            $questions = $em->getRepository('AppBundle:Questions');

            return $this->createSuccessResponse(
                [
                    'questions' => $questions->getEntitiesByParams($paramFetcher),
                    'total' => $questions->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_questions', 'get_user_question_answer_test'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create/Put question by admin.
     * <strong>Simple example:</strong><br />
     * http://host/api/question <br>.
     *
     * @Rest\Post("/api/question")
     * @ApiDoc(
     * resource = true,
     * description = "Create/Put question by admin",
     * authentication=true,
     *  parameters={
     *      {"name"="id", "dataType"="string", "required"=false, "description"="question id"},
     *      {"name"="custom_id", "dataType"="string", "required"=false, "description"="custom id"},
     *      {"name"="year", "dataType"="integer", "required"=false, "description"="year"},
     *      {"name"="type", "dataType"="enum", "required"=true, "description"="open or test"},
     *      {"name"="question_number", "dataType"="integer", "required"=false, "description"="question number"},
     *      {"name"="notes", "dataType"="text", "required"=false, "description"="notes"},
     *      {"name"="text", "dataType"="text", "required"=true, "description"="notes"},
     *      {"name"="semesters", "dataType"="integer", "required"=true, "description"="semesters id"},
     *      {"name"="exam_periods", "dataType"="integer", "required"=true, "description"="exam periods id"},
     *      {"name"="sub_courses", "dataType"="integer", "required"=true, "description"="sub courses id"},
     *      {"name"="lectors", "dataType"="integer", "required"=true, "description"="lectors id"},
     *      {"name"="image_url", "dataType"="file", "required"=false, "description"="file for upload"},
     *      {"name"="question_answers", "dataType"="array", "required"=false, "description"="question answers array objects"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question"
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
    public function postAdminQuestionsAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $serializerGroup = 'post_question';
            $persist = true;
            $questions = null;
            if ($request->get('id')) {
                $questions = $em->getRepository('AppBundle\Entity\Questions')
                    ->findOneBy(['id' => $request->get('id')]);
            }
            if ($questions instanceof Questions) {
                $request->request->set('id', $questions->getId());
                $serializerGroup = 'put_question';
                $persist = false;
            }

            $this->prepareAuthor();

            /** @var Questions $questions */
            $questions = $auth->validateEntites('request', Questions::class, [$serializerGroup]);

            if ($request->files->get('image_url') instanceof UploadedFile) {
                $service = $this->get('app.file_uploader');

                $fileName = $service->upload(
                    $request->files->get('image_url'),
                    FileUploader::LOCAL_STORAGE
                );

                $questions->setImageUrl('/files/'.$fileName);
            }

            !$persist ?: $em->persist($questions);
            $em->flush();

            return $this->createSuccessResponse($questions, ['get_question'], true);
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
     * Delete question by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/question/{id} <br>.
     *
     * @Rest\Delete("/api/admins/question/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete question by Admin",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Admins Question"
     * )
     *
     * @RestView()
     *
     * @param Questions $questions
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedQuestionsAction(Questions $questions)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($questions);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
