<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\RepeatedQuestions;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RepeatedQuestionsController extends AbstractRestController
{
    /**
     * Create repeatedQuestions.
     * <strong>Simple example:</strong><br />
     * http://host/api/repeated_questions <br>.
     *
     * @Rest\Post("/api/repeated_questions")
     * @ApiDoc(
     * resource = true,
     * description = "Create repeatedQuestions",
     * authentication=true,
     *  parameters={
     *      {"name"="user", "dataType"="integer", "required"=true, "description"="user id or user object"},
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions id or object"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="RepeatedQuestions"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postRepeatedQuestionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $this->prepareAuthor();
            /** @var RepeatedQuestions $entity */
            $entity = $auth->validateEntites('request', RepeatedQuestions::class, ['post_repeated_questions']);

            $em->persist($entity);
            $em->flush();

            return $this->createSuccessResponse($entity, ['get_repeated_questions'], true);
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
     * Delete repeated_questions.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/repeated_questions/{id} <br>.
     *
     * @Rest\Delete("/api/repeated_questions/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete repeated_questions",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="RepeatedQuestions"
     * )
     *
     * @RestView()
     *
     * @param RepeatedQuestions $questions
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedFavoritesAction(RepeatedQuestions $questions)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            if ($questions->getUser() !== $this->getUser()) {
                throw new AccessDeniedException();
            }
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
