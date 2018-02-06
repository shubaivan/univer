<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Comments;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentsController extends AbstractRestController
{
    /**
     * Get comment by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/comment/{id} <br>.
     *
     * @Rest\Get("/api/comment/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get comment by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Comment"
     * )
     *
     * @RestView()
     *
     * @param Comments $comments
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getCommentsAction(Comments $comments)
    {
        return $this->createSuccessResponse($comments, ['get_comment'], true);
    }

    /**
     * Get list comments.
     * <strong>Simple example:</strong><br />
     * http://host/api/comments <br>.
     *
     * @Rest\Get("/api/comments")
     * @ApiDoc(
     * resource = true,
     * description = "Get list comments",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Comment"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search fields - text")
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
    public function getCommentAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $paramFetcher = $this->responsePrepareAuthor($paramFetcher);
            $comments = $em->getRepository('AppBundle:Comments');

            return $this->createSuccessResponse(
                [
                    'comments' => $comments->getEntitiesByParams($paramFetcher),
                    'total' => $comments->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_comments'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create comment.
     * <strong>Simple example:</strong><br />
     * http://host/api/comment <br>.
     *
     * @Rest\Post("/api/comment")
     * @ApiDoc(
     * resource = true,
     * description = "Create comment",
     * authentication=true,
     *  parameters={
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions id or object"},
     *      {"name"="user", "dataType"="integer", "required"=true, "description"="user id or object"},
     *      {"name"="text", "dataType"="text", "required"=true, "description"="text"},
     *      {"name"="reply_comments", "dataType"="text", "required"=false, "description"="reply comment (!only) object"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Comment"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postCommentsAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $this->prepareAuthor();
            /** @var Comments $comments */
            $comments = $auth->validateEntites('request', Comments::class, ['post_comment']);

            $em->persist($comments);
            $em->flush();

            return $this->createSuccessResponse($comments, ['get_comment'], true);
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
     * Put comment.
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/comment/{id} <br>.
     *
     * @Rest\Put("/api/admins/comment/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put comment",
     * authentication=true,
     *  parameters={
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions id or object"},
     *      {"name"="user", "dataType"="integer", "required"=true, "description"="user id or object"},
     *      {"name"="text", "dataType"="text", "required"=true, "description"="text"},
     *      {"name"="reply", "dataType"="text", "required"=false, "description"="reply comment (!only) object"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Comment"
     * )
     *
     * @RestView()
     *
     * @param Request  $request
     * @param Comments $comments
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putCommentsAction(Request $request, Comments $comments)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $comments->getId());
            /** @var Comments $comments */
            $comments = $auth->validateEntites('request', Comments::class, ['put_comment']);

            $em->flush();

            return $this->createSuccessResponse($comments, ['get_comment'], true);
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
     * Delete comment.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/admins/comment/{id} <br>.
     *
     * @Rest\Delete("/api/admins/comment/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete comment",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Comment"
     * )
     *
     * @RestView()
     *
     * @param Comments $comments
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedCommentsAction(Comments $comments)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($comments);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
