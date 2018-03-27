<?php

namespace AppBundle\Application\Notifications;

use AppBundle\Domain\Notifications\NotificationsDomain;
use AppBundle\Entity\Enum\ImprovementSuggestionStatusEnum;
use AppBundle\Entity\User;
use AppBundle\Model\Request\NotificationsRequestModel;
use Symfony\Component\HttpFoundation\ParameterBag;

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
        $providerId,
        $message,
        $native = false
    ) {
        $prepareData = [
            'user' => [
                'id' => $user->getId(),
            ],
            'sender' => [
                'id' => $sender->getId(),
            ],
            'provider' => $provider,
            'provider_id' => $providerId,
            'message' => $message,
        ];
        if ($native) {
            $parameterBag = new ParameterBag();
            $parameterBag->set('user', $user->getId());
            $parameterBag->set('sender', $sender->getId());
            $parameterBag->set('provider', $provider);
            $parameterBag->set('providerId', $providerId);
            $parameterBag->set('message', $message);
            $parameterBag->set('status', ImprovementSuggestionStatusEnum::NOT_VIEWED);

            $this->getNotificationsDomain()
                ->createNotifications($parameterBag);
        } else {
            $this->getNotificationsDomain()
                ->handleNotificationPrepareData($prepareData);
        }
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
