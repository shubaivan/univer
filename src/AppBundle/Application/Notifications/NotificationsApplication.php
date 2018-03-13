<?php

namespace AppBundle\Application\Notifications;

use AppBundle\Domain\Notifications\NotificationsDomain;
use AppBundle\Entity\User;

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
     * @return NotificationsDomain
     */
    private function getNotificationsDomain()
    {
        return $this->notificationsDomain;
    }
}
