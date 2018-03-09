<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Events;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * EventsRepository.
 */
class EventsRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return Events[]|int
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $params = $paramFetcher->getParams();
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT e.id)
                ');
        } else {
            $qb
                ->select('
                    e                  
                ');
        }

        $qb
            ->from('AppBundle:Events', 'e');

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('e.user', $paramFetcher->get('user')));
        }

        if (!$count) {
            $qb
                ->orderBy('e.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
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
}
