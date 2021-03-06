<?php

namespace AppBundle\Domain\Notifications;

use AppBundle\Entity\Notifications;
use AppBundle\Entity\Repository\NotificationsRepository;
use AppBundle\Services\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class NotificationsDomain implements NotificationsDomainInterface
{
    /**
     * @var NotificationsRepository
     */
    private $notificationsRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * NotificationsDomain constructor.
     *
     * @param NotificationsRepository $notificationsRepository
     * @param ObjectManager           $objectManager
     * @param EntityManager           $entityManager
     */
    public function __construct(
        NotificationsRepository $notificationsRepository,
        ObjectManager $objectManager,
        EntityManager $entityManager
    ) {
        $this->notificationsRepository = $notificationsRepository;
        $this->objectManager = $objectManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $data
     *
     * @return Notifications
     */
    public function handleNotificationPrepareData($data)
    {
        /** @var Notifications $notification */
        $notification = $this->getObjectManager()
            ->validateEntites(
                '',
                Notifications::class,
                Notifications::getPostGroup(),
                $data
            );
        $this->getEntityManager()->persist($notification);

        return $notification;
    }

    /**
     * @param array  $ids
     * @param string $status
     *
     * @return bool
     */
    public function handleUpdateStatus($ids, $status)
    {
        if (!$ids) {
            return false;
        }
        $this->getNotificationsRepository()
            ->updateStatus($ids, $status);

        return true;
    }

    /**
     * @param ParameterBag $parameterBag
     */
    public function createNotifications(ParameterBag $parameterBag)
    {
        $this->getNotificationsRepository()
            ->createNotifications($parameterBag);
    }

    /**
     * @return NotificationsRepository
     */
    private function getNotificationsRepository()
    {
        return $this->notificationsRepository;
    }

    /**
     * @return ObjectManager
     */
    private function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->entityManager;
    }
}
