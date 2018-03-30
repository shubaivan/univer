<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Events;
use AppBundle\Entity\Questions;
use AppBundle\Entity\Repository\QuestionsRepository;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Helper\FileUploader;
use AppBundle\Services\ObjectManager;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
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
     * http://host/api/list/questions <br>.
     *
     * @Rest\Post("/api/list/questions")
     * @ApiDoc(
     * resource = true,
     * description = "Get list questions",
     * authentication=true,
     *  parameters={
     *      {"name"="event_id", "dataType"="object", "required"=false, "description"="event object"},
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
     *      {"name"="user_state", "dataType"="enum", "required"=false, "description"="user state - not_successed, unresolved"},
     *      {"name"="repeated", "dataType"="array", "required"=false, "description"="repeatd array with one elemnt true/false"},
     *      {"name"="votes", "dataType"="boolean", "required"=false, "description"="true/false"},
     *      {"name"="search", "dataType"="text", "required"=false, "description"="search filed - questionNumber, notes, text"}
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
    public function getQuestionsListAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            /** @var QuestionsRepository $questions */
            $questions = $em->getRepository(Questions::class);

            $event = $em->getRepository('AppBundle:Events')
                ->findOneBy(['id' => $request->get('event_id')]);
            if ($event) {
                $parameterBag = $event->checkCondition();
            } else {
                /** @var ObjectManager $auth */
                $auth = $this->get('app.auth');
                $this->prepareAuthor();
                /** @var Events $events */
                $events = $auth->validateEntites('request', Events::class, ['post_event']);
                $parameterBag = $events->checkCondition();
                if ($parameterBag->count() > 4) {
                    $em->persist($events);
                    $em->flush();
                }
            }

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
     *      {"name"="correction_id", "dataType"="integer", "required"=false, "description"="correction question id"},
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
     *      {"name"="question_answers", "dataType"="array", "required"=false, "description"="question answers array objects"},
     *      {"name"="courses", "dataType"="integer", "required"=true, "description"="courses id or object"},
     *      {"name"="courses_of_study", "dataType"="integer", "required"=true, "description"="coursesOfStudy id or object"},
     *      {"name"="votes_at", "dataType"="datetime", "required"=true, "format" = "Y-m-d H:i:s", "description"="DatTime for clear votes"}
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
    public function postQuestionsAction(Request $request)
    {
        /** @var EntityManager $em */
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
                $questions->setUpdatedAt(new \DateTime());
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
            $em->refresh($questions);

            if ($em->getRepository('AppBundle:QuestionCorrections')
                ->findOneBy(['id' => $request->get('correction_id')])) {

                $em->remove(
                    $em->getRepository('AppBundle:QuestionCorrections')
                        ->findOneBy(['id' => $request->get('correction_id')])
                );

                $em->flush();
            }

            return $this->createSuccessResponse($questions, ['get_question'], true);
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
     * Delete votes question by Admin.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/question/votes/{id} <br>.
     *
     * @Rest\Delete("/api/admins/question/votes/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete votes question by Admin",
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
    public function deletedVotesQuestionsAction(Questions $questions)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();

        try {
            $votes = $em->getRepository('AppBundle:Votes')
                ->findBy(['questions' => $questions]);
            foreach ($votes as $vote) {
                $em->remove($vote);
            }

            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
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
