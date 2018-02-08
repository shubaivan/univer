<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Favorites;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoritesController extends AbstractRestController
{
    /**
     * Get favorite by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/favorite/{id} <br>.
     *
     * @Rest\Get("/api/favorite/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get favorite by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Favorite"
     * )
     *
     * @RestView()
     *
     * @param Favorites $favorites
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getFavoritesAction(Favorites $favorites)
    {
        return $this->createSuccessResponse($favorites, ['get_favorite'], true);
    }

    /**
     * Get list favorites.
     * <strong>Simple example:</strong><br />
     * http://host/api/favorites <br>.
     *
     * @Rest\Get("/api/favorites")
     * @ApiDoc(
     * resource = true,
     * description = "Get list favorites",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Favorite"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="search", description="search fields - questions text, notes; user firstName, lastName, username, email")
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
    public function getFavoriteAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $favorites = $em->getRepository('AppBundle:Favorites');
            $paramFetcher = $this->responsePrepareAuthor($paramFetcher);

            return $this->createSuccessResponse(
                [
                    'favorites' => $favorites->getEntitiesByParams($paramFetcher),
                    'total' => $favorites->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_favorites', 'get_question'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create favorite.
     * <strong>Simple example:</strong><br />
     * http://host/api/favorite <br>.
     *
     * @Rest\Post("/api/favorite")
     * @ApiDoc(
     * resource = true,
     * description = "Create favorite",
     * authentication=true,
     *  parameters={
     *      {"name"="user", "dataType"="integer", "required"=true, "description"="user id or user object"},
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions id or object"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Favorite"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postFavoritesAction()
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $this->prepareAuthor();
            /** @var Favorites $favorites */
            $favorites = $auth->validateEntites('request', Favorites::class, ['post_favorite']);

            $em->persist($favorites);
            $em->flush();

            return $this->createSuccessResponse($favorites, ['get_favorite'], true);
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
     * Put favorite.
     * <strong>Simple example:</strong><br />
     * http://host/api/favorite/{id} <br>.
     *
     * @Rest\Put("/api/favorite/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Put favorite",
     * authentication=true,
     *  parameters={
     *      {"name"="user", "dataType"="integer", "required"=true, "description"="user id or user object"},
     *      {"name"="questions", "dataType"="integer", "required"=true, "description"="questions id or object"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Favorite"
     * )
     *
     * @RestView()
     *
     * @param Request   $request
     * @param Favorites $favorites
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function putFavoriteAction(Request $request, Favorites $favorites)
    {
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $request->request->set('id', $favorites->getId());
            $this->prepareAuthor();
            /** @var Favorites $favorites */
            $favorites = $auth->validateEntites('request', Favorites::class, ['put_favorite']);

            $em->flush();

            return $this->createSuccessResponse($favorites, ['get_favorite'], true);
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
     * Delete favorite.
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/favorite/{id} <br>.
     *
     * @Rest\Delete("/api/favorite/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete favorite",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Favorite"
     * )
     *
     * @RestView()
     *
     * @param Favorites $favorites
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedFavoritesAction(Favorites $favorites)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($favorites);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
