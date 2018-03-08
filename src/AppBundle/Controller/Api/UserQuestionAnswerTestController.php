<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\UserQuestionAnswerTest;
use AppBundle\Exception\ValidatorException;
use AppBundle\Model\Request\UserQuestionAnswerTestRequestModel;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserQuestionAnswerTestController extends AbstractRestController
{
    /**
     * Get user_question_answer_test by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/user_question_answer_test/{id} <br>.
     *
     * @Rest\Get("/api/user_question_answer_test/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get user_question_answer_test by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="UserQuestionAnswerTest"
     * )
     *
     * @RestView()
     *
     * @param UserQuestionAnswerTest $questionAnswerTest
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getFavoritesAction(UserQuestionAnswerTest $questionAnswerTest)
    {
        return $this->createSuccessResponse(
            $questionAnswerTest,
            ['get_user_question_answer_test'],
            true
        );
    }

    /**
     * Create user_question_answer_test.
     * <strong>Simple example:</strong><br />
     * http://host/api/user_question_answer_test <br>.
     *
     * @Rest\Post("/api/user_question_answer_test")
     * @ApiDoc(
     * resource = true,
     * description = "Create user_question_answer_test",
     * authentication=true,
     *  parameters={
     *      {"name"="answers",
     *       "dataType"="array",
     *       "required"=true,
     *       "format" = "[{question_answers: {id: integer}, result: boolean}]",
     *       "description"="array objects"
     *      }
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="UserQuestionAnswerTest"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postUserQuestionAnswerTestAction()
    {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            /** @var UserQuestionAnswerTestRequestModel $model */
            $model = $auth->validateEntites(
                'request',
                UserQuestionAnswerTestRequestModel::class,
                ['post_user_question_answer_test']
            );
            foreach ($model->getAnswers() as $answer) {
                $em->persist($answer);
            }

            $em->flush();

            return $this->createSuccessResponse(
                $model,
                ['get_user_question_answer_test'],
                true
            );
        } catch (ValidatorException $e) {
            $view = $this->view($e->getConstraintViolatinosList(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
