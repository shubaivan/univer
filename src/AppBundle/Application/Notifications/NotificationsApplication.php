<?php

namespace AppBundle\Application\Notifications;

use AppBundle\Domain\Notifications\NotificationsDomain;
use AppBundle\Entity\User;
use AppBundle\Model\Request\NotificationsRequestModel;

class NotificationsApplication implements NotificationsApplicationInterface
{
    /**
     * @var NotificationsDomain
     */
    private $notificationsDomain;

    /**
     * NotificationsApplication constructor.
     *
     * @param NotificationsDomain $notificationsDomain
     */
    public function __construct(
        NotificationsDomain $notificationsDomain
    ) {
        $this->notificationsDomain = $notificationsDomain;
    }

    public function createNotification(
        User $user,
        User $sender,
        $provider,
        $message
    ) {
        $prepareData = [
            'user' => [
                'id' => $user->getId(),
            ],
            'sender' => [
                'id' => $sender->getId(),
            ],
            'provider' => $provider,
            'message' => $message,
        ];

        $this->getNotificationsDomain()
            ->handleNotificationPrepareData($prepareData);
    }

    /**
     * @param NotificationsRequestModel $model
     */
    public function changeStatusNotifications(NotificationsRequestModel $model)
    {
        $ids = [];
        foreach ($model->getNotifications() as $notification) {
            $ids[] = $notification->getId();
        }

        $this->getNotificationsDomain()
            ->handleUpdateStatus($ids, $model->getStatus());
    }

    /**
     * @return NotificationsDomain
     */
    private function getNotificationsDomain()
    {
        return $this->notificationsDomain;
    }
}
