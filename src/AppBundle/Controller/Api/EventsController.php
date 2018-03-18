<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Events;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventsController extends AbstractRestController
{
    /**
     * Get list events
     * <strong>Simple example:</strong><br />
     * http://host/api/events <br>.
     *
     * @Rest\Get("/api/events")
     * @ApiDoc(
     * resource = true,
     * description = "Get list events",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Events"
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
    public function getEventsAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('AppBundle:Events');
            $paramFetcher = $this->responsePrepareAuthor($paramFetcher);

            return $this->createSuccessResponse(
                [
                    'events' => $repository->getEntitiesByParams($paramFetcher),
                    'total' => $repository->getEntitiesByParams($paramFetcher, true),
                ],
                ['get_events'],
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Delete event
     *
     * <strong>Simple example:</strong><br />
     * http://host/api/events/{id} <br>.
     *
     * @Rest\Delete("/api/events/{id}")
     * @ApiDoc(
     *      resource = true,
     *      description = "Delete event",
     *      authentication=true,
     *      parameters={
     *
     *      },
     *      statusCodes = {
     *          200 = "Returned when successful",
     *          400 = "Returned bad request"
     *      },
     *      section="Events"
     * )
     *
     * @RestView()
     *
     * @param Events $events
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function deletedEventsAction(Events $events)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($events);
            $em->flush();

            return $this->createSuccessStringResponse(self::DELETED_SUCCESSFULLY);
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }
}
