<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Events;
use AppBundle\Entity\QuestionCorrections;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Helper\FileUploader;
use AppBundle\Services\ObjectManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuestionCorrectionsController extends AbstractRestController
{
    /**
     * Get QuestionCorrections by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/question_corrections/{id} <br>.
     *
     * @Rest\Get("/api/question_corrections/{id}")
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
     * section="Question_Corrections"
     * )
     *
     * @RestView()
     *
     * @param QuestionCorrections $questionCorrections
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getQuestionCorrectionsAction(QuestionCorrections $questionCorrections)
    {
        return $this->createSuccessResponse(
            $questionCorrections,
            ['get_question', 'get_user_question_answer_test'],
            true
        );
    }

    /**
     * Get list corrections questions.
     * <strong>Simple example:</strong><br />
     * http://host/api/list/question_corrections <br>.
     *
     * @Rest\Get("/api/list/questions")
     * @ApiDoc(
     * resource = true,
     * description = "Get list corrections questions",
     * authentication=true,
     *  parameters={
     *      {"name"="courses_of_study", "dataType"="object", "required"=false, "description"="courses_of_study object"},
     *      {"name"="courses", "dataType"="array<object>", "required"=false, "description"="courses array object"},
     *      {"name"="sub_courses", "dataType"="array<object>", "required"=false, "description"="sub_courses array object"},
     *      {"name"="lectors", "dataType"="array<object>", "required"=false, "description"="lecturer array object"},
     *      {"name"="exam_periods", "dataType"="array<object>", "required"=false, "description"="exam_periods array object"},
     *      {"name"="semesters", "dataType"="array<object>", "required"=false, "description"="semesters array object"},
     *      {"name"="count", "dataType"="integer", "required"=false, "description"="count"},
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="page"},
     *      {"name"="sort_by", "dataType"="text", "required"=false, "description"="sort_by"},
     *      {"name"="sort_order", "dataType"="text", "required"=false, "description"="sort_by"},
     *      {"name"="years", "dataType"="text", "required"=false, "description"="years, delimiter (,)"},
     *      {"name"="search", "dataType"="text", "required"=false, "description"="search fields - text, notes"},
     *      {"name"="user", "dataType"="text", "required"=false, "description"="user object"},
     *      {"name"="user_state", "dataType"="enum", "required"=false, "description"="user state - not_successed, unresolved"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question_Corrections"
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
    public function getQuestionCorrectionsListAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            /** @var ObjectManager $auth */
            $auth = $this->get('app.auth');
            $this->prepareAuthor();
//            /** @var Events $events */
            $events = $auth->validateEntites('request', Events::class, ['post_event']);
            $em->persist($events);
            $em->flush();
            $parameterBag = $events->checkCondition();
            $questions = $em->getRepository(QuestionCorrections::class);

            return $this->createSuccessResponse(
                [
                    'questions' => $questions->getEntitiesByParams($parameterBag),
                    'total' => $questions->getEntitiesByParams($parameterBag, true),
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
     * Create/Put questionCorrections .
     * <strong>Simple example:</strong><br />
     * http://host/api/question_corrections <br>.
     *
     * @Rest\Post("/api/question_corrections")
     * @ApiDoc(
     * resource = true,
     * description = "Create/Put questionCorrections  ",
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
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions_id"},
     *      {"name"="exam_periods", "dataType"="integer", "required"=true, "description"="exam periods id"},
     *      {"name"="sub_courses", "dataType"="integer", "required"=true, "description"="sub courses id"},
     *      {"name"="lectors", "dataType"="integer", "required"=true, "description"="lectors id"},
     *      {"name"="image_url", "dataType"="file", "required"=false, "description"="file for upload"},
     *      {"name"="question_answers_corrections", "dataType"="array", "required"=false, "description"="question answers array objects"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Question_Corrections"
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
    public function postQuestionCorrectionsAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $serializerGroup = 'post_question_corrections';
            $persist = true;
            $questionCorrections = null;
            if ($request->get('id')) {
                $questionCorrections = $em->getRepository('AppBundle\Entity\QuestionCorrections')
                    ->findOneBy(['id' => $request->get('id')]);
            }
            if ($questionCorrections instanceof QuestionCorrections) {
                $request->request->set('id', $questionCorrections->getId());
                $serializerGroup = 'put_question';
                $persist = false;
            }

            $this->prepareAuthor();

            /** @var QuestionCorrections $questionCorrections */
            $questionCorrections = $auth->validateEntites('request', QuestionCorrections::class, [$serializerGroup]);

            if ($request->files->get('image_url') instanceof UploadedFile) {
                $service = $this->get('app.file_uploader');

                $fileName = $service->upload(
                    $request->files->get('image_url'),
                    FileUploader::LOCAL_STORAGE
                );

                $questionCorrections->setImageUrl('/files/'.$fileName);
            }

            !$persist ?: $em->persist($questionCorrections);
            $em->flush();

            return $this->createSuccessResponse($questionCorrections, ['get_question_corrections'], true);
        } catch (ValidatorException $e) {
            $view = $this->view($e->getConstraintViolatinosList(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

//    /**
//     * Delete question by Admin.
//     *
//     * <strong>Simple example:</strong><br />
//     * http://host/api/admins/question/{id} <br>.
//     *
//     * @Rest\Delete("/api/admins/question/{id}")
//     * @ApiDoc(
//     *      resource = true,
//     *      description = "Delete question by Admin",
//     *      authentication=true,
//     *      parameters={
//     *
//     *      },
//     *      statusCodes = {
//     *          200 = "Returned when successful",
//     *          400 = "Returned bad request"
//     *      },
//     *      section="Admins Question"
//     * )
//     *
//     * @RestView()
//     *
//     * @param Questions $questions
//     *
//     * @throws NotFoundHttpException when not exist
//     *
//     * @return Response|View
//     */
//    public function deletedQuestionsAction(Questions $questions)
//    {
//        $em = $this->get('doctrine')->getManager();
//
//        try {
//            $em->remove($questions);
//            $em->flush();
//
//            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
//        } catch (\Exception $e) {
//            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
//            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
//        }
//
//        return $this->handleView($view);
//    }
}
