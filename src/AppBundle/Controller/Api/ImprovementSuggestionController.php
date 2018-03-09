<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\AbstractUser;
use AppBundle\Entity\Enum\ImprovementSuggestionStatusEnum;
use AppBundle\Entity\ImprovementSuggestions;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ImprovementSuggestionController extends AbstractRestController
{
    /**
     * Get improvement by id.
     * <strong>Simple example:</strong><br />
     * http://host/api/improvement/{id} <br>.
     *
     * @Rest\Get("/api/improvement/{id}")
     * @ApiDoc(
     * resource = true,
     * description = "Get improvement by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Improvement"
     * )
     *
     * @RestView()
     *
     * @param ImprovementSuggestions $improvements
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getImprovementAction(ImprovementSuggestions $improvements)
    {
        try {
            if (!$this->getUser()->hasRole(AbstractUser::ROLE_ADMIN)
                && $this->getUser() !== $improvements->getUser())
            {
                throw new AccessDeniedException();
            } elseif ($this->getUser()->hasRole(AbstractUser::ROLE_ADMIN)) {
                $improvements->setStatus(ImprovementSuggestionStatusEnum::VIEWED);

                $this->getDoctrine()->getManager()->flush();
            }
            return $this->createSuccessResponse($improvements, ['get_improvement_suggestions'], true);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Get list improvements.
     * <strong>Simple example:</strong><br />
     * http://host/api/improvements <br>.
     *
     * @Rest\Get("/api/improvements")
     * @ApiDoc(
     * resource = true,
     * description = "Get list improvements",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Improvement"
     * )
     *
     * @RestView()
     *
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
    public function getImprovementsAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $improvements = $em->getRepository('AppBundle:ImprovementSuggestions');
            $paramFetcher = $this->responsePrepareAuthor($paramFetcher);

            return $this->createSuccessResponse(
                [
                    'improvements' => $improvements->getEntitiesByParams($paramFetcher),
                    'total' => $improvements->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_improvement_suggestions'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Create improvement.
     * <strong>Simple example:</strong><br />
     * http://host/api/improvement <br>.
     *
     * @Rest\Post("/api/improvement")
     * @ApiDoc(
     * resource = true,
     * description = "Create improvement",
     * authentication=true,
     *  parameters={
     *      {"name"="description", "dataType"="text", "required"=true, "description"="description"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Improvement"
     * )
     *
     * @RestView()
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function postImprovementsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');
            $this->prepareAuthor();
            /** @var ImprovementSuggestions $improvements */
            $improvements = $auth->validateEntites('request', ImprovementSuggestions::class, ['post_improvement_suggestions']);

            $em->persist($improvements);
            $em->flush();

            return $this->createSuccessResponse($improvements, ['get_improvement_suggestions'], true);
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
