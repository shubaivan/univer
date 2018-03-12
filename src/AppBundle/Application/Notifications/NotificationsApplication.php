<?php

namespace AppBundle\Application\Notifications;

use AppBundle\Domain\Notifications\NotificationsDomain;
use AppBundle\Domain\SubCourses\SubCoursesDomainInterface;
use AppBundle\Entity\Collections\SubCourses\SubCoursesCollection;
use FOS\RestBundle\Request\ParamFetcher;

class NotificationsApplication implements NotificationsApplicationInterface
{
    /**
     * @var NotificationsDomain
     */
    private $notificationsDomain;

    /**
     * NotificationsApplication constructor.
     * @param NotificationsDomain $notificationsDomain
     */
    public function __construct(
        NotificationsDomain $notificationsDomain
    ) {
        $this->notificationsDomain = $notificationsDomain;
    }

    /**
     * @return NotificationsDomain
     */
    private function getNotificationsDomain()
    {
        return $this->notificationsDomain;
    }
}
