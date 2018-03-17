<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Votes;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class VotesController extends AbstractRestController
{
    /**
     * Create votes.
     * <strong>Simple example:</strong><br />
     * http://host/api/votes <br>.
     *
     * @Rest\Post("/api/votes")
     * @ApiDoc(
     * resource = true,
     * description = "Create votes",
     * authentication=true,
     *  parameters={
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions id or object"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Votes"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postVotesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $this->prepareAuthor();
            /** @var Votes $entity */
            $entity = $auth->validateEntites('request', Votes::class, ['post_votes']);

            $em->persist($entity);
            $em->flush();

            return $this->createSuccessResponse($entity, ['get_votes'], true);
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
     * Delete votes.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/votes/{id} <br>.
     *
     * @Rest\Delete("/api/votes/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete votes",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Votes"
     * )
     *
     * @RestView()
     *
     * @param Votes $votes
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedVotesAction(Votes $votes)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            if ($votes->getUser() !== $this->getUser()) {
                throw new AccessDeniedException();
            }
            $em->remove($votes);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
