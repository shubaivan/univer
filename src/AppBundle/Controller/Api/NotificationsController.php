<?php

namespace AppBundle\Controller\Api;

use AppBundle\Application\Notifications\NotificationsApplication;
use AppBundle\Entity\Notifications;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Model\Request\NotificationsRequestModel;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationsController extends AbstractRestController
{
    /**
     * Get list notifications.
     * <strong>Simple example:</strong><br />
     * http://host/api/notifications <br>.
     *
     * @Rest\Get("/api/notifications")
     * @ApiDoc(
     * resource = true,
     * description = "Get list notifications",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Notification"
     * )
     *
     * @RestView()
     *
     * @Rest\QueryParam(name="status", requirements="^[a-zA-Z]+", description="status notification, enum - not_viewed, viewed")
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="user", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @throws NotFoundHttpException when not exist
     *
     * @return Response|View
     */
    public function getNotificationAction(ParamFetcher $paramFetcher)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $notifications = $em->getRepository('AppBundle:Notifications');
            $paramFetcher = $this->responsePrepareAuthor($paramFetcher);

            return $this->createSuccessResponse(
                [
                    'notifications' => $notifications->getEntitiesByParams($paramFetcher),
                    'total' => $notifications->getEntitiesByParams($paramFetcher, true),
                ],
                Notifications::getGetGroup(),
                true
            );
        } catch (\Exception $e) {
            $view = $this->view((array) $e->getMessage(), self::HTTP_STATUS_CODE_BAD_REQUEST);
            $this->getLogger()->error($this->getMessagePrefix().'error: '.$e->getMessage());
        }

        return $this->handleView($view);
    }

    /**
     * Put Notification.
     * <strong>Simple example:</strong><br />
     * http://host/api/notifications <br>.
     *
     * @Rest\Put("/api/notifications")
     * @ApiDoc(
     * resource = true,
     * description = "Put Notification",
     * authentication=true,
     *  parameters={
     *      {"name"="notifications",
     *       "dataType"="array",
     *       "required"=true,
     *       "format" = "[{notifications: {id: integer}]",
     *       "description"="notifications object"
     *      },
     *      {"name"="status", "dataType"="text", "required"=true, "description"="enum status - viewed, not_viewed"}
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Notification"
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
    public function putNotificationsAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        $logger = $this->container->get('logger');

        try {
            $auth = $this->get('app.auth');

            $this->prepareAuthor();
            /** @var NotificationsRequestModel $notifications */
            $notifications = $auth->validateEntites(
                'request',
                NotificationsRequestModel::class,
                NotificationsRequestModel::getPostGroup()
            );
            /** @var NotificationsApplication $app */
            $app = $this->get('app.application.notifications_application');
            $app->changeStatusNotifications($notifications);

            return $this->createSuccessResponse($notifications, ['get_notifications', 'profile'], true);
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
