<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Notifications;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * NotificationsRepository.
 */
class NotificationsRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return int|Notifications[]
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $em = $this->getEntityManager();
        $params = $paramFetcher->getParams();
        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT n.id)
                ');
        } else {
            $qb
                ->select('
                    n
                ');
        }

        $qb
            ->from('AppBundle:Notifications', 'n');

        if (array_key_exists('status', $params) && $paramFetcher->get('status')) {
            $qb
                ->andWhere('n.status = :status')
                ->setParameter('status', $paramFetcher->get('status'));
        }

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('n.user', $paramFetcher->get('user')));
        }

        if (!$count) {
            $qb
                ->orderBy('n.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'));
        }

        $query = $qb->getQuery();

        if ($count) {
            $results = $query->getSingleScalarResult();
        } else {
            $results = $query->getResult();
        }

        return $results;
    }

    /**
     * @param $ids
     * @param $status
     */
    public function updateStatus($ids, $status)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb
            ->update('AppBundle:Notifications', 'n')
            ->set('n.status', $qb->expr()->literal($status))
            ->where($qb->expr()->in('n.id', $ids));

        $qb->getQuery()->execute();
    }
}
