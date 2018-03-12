<?php

namespace AppBundle\Domain\Notifications;

use AppBundle\Entity\Repository\NotesRepository;
use AppBundle\Entity\Repository\NotificationsRepository;
use AppBundle\Entity\Repository\QuestionsRepository;
use AppBundle\Entity\Repository\SubCoursesRepository;
use FOS\RestBundle\Request\ParamFetcher;

class NotificationsDomain implements NotificationsDomainInterface
{
    /**
     * @var NotificationsRepository
     */
    private $notificationsRepository;

    /**
     * NotificationsDomain constructor.
     * @param NotificationsRepository $notificationsRepository
     */
    public function __construct(
        NotificationsRepository $notificationsRepository
    ) {
        $this->notificationsRepository = $notificationsRepository;
    }

    /**
     * @return NotificationsRepository
     */
    private function getNotificationsRepository()
    {
        return $this->notificationsRepository;
    }
}
