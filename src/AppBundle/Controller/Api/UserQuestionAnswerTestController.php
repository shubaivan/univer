<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Favorites;
use AppBundle\Entity\User;
use AppBundle\Entity\UserQuestionAnswerTest;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserQuestionAnswerTestController extends AbstractRestController
{
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
     *      {"name"="question_answers", "dataType"="integer", "required"=true, "description"="question_answers id or object"},
     *      {"name"="result", "dataType"="boolean", "required"=true, "description"="result"}
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
            $this->prepareAuthor();
            /** @var UserQuestionAnswerTest $userQuestionAnswerTest */
            $userQuestionAnswerTest = $auth->validateEntites(
                'request',
                UserQuestionAnswerTest::class,
                ['post_user_question_answer_test']
            );

            $em->persist($userQuestionAnswerTest);
            $em->flush();

            return $this->createSuccessResponse(
                $userQuestionAnswerTest,
                ['get_user_question_answer_test'],
                true
            );
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrorsMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'validate error: '.$e->getErrorsMessage());
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $logger->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
