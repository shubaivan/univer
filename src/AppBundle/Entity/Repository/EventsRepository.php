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

        $qbExcludedEventsResult = $em->createQueryBuilder();
        $qbExcludedEventsResult
            ->select('events.id')
            ->from('AppBundle:Events', 'events')

            ->where($qbExcludedEventsResult->expr()->isNull('events.coursesOfStudy'))
            ->leftJoin('events.courses', 'courses')
            ->andWhere('courses.id IS NULL')
            ->leftJoin('events.examPeriods', 'examPeriods')
            ->andWhere('examPeriods.id IS NULL')
            ->leftJoin('events.lectors', 'lectors')
            ->andWhere('lectors.id IS NULL')
            ->leftJoin('events.semesters', 'semesters')
            ->andWhere('semesters.id IS NULL')
            ->leftJoin('events.subCourses', 'subCourses')
            ->andWhere('subCourses.id IS NULL')
            ->andWhere('events.search IS NULL')
            ->andWhere('events.repeated = :repeated')
            ->andWhere('events.userState IS NULL')
            ->andWhere('events.years = :years')
            ->setParameter('repeated', serialize([]))
            ->setParameter('years', serialize([]));

        $qbExcludedEventsResult
            ->expr()->orX(
                $qbExcludedEventsResult->expr()->isNotNull('events.votes'),
                $qbExcludedEventsResult->expr()->isNotNull('events.search')
            );

        $qb
            ->andWhere($qb->expr()->notIn('e.id', $qbExcludedEventsResult->getDQL()))
            ->setParameter('repeated', serialize([]))
            ->setParameter('years', serialize([]));

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
