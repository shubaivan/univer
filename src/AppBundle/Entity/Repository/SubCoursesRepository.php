<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\SubCourses;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * SubCoursesRepository.
 */
class SubCoursesRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return SubCourses[]|int
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT s.id)
                ');
        } else {
            $qb
                ->select('s');
        }

        $qb
            ->from('AppBundle:SubCourses', 's');

        if ($paramFetcher->get('search')) {
            $andXSearch = $qb->expr()->andX();

            foreach (explode(' ', $paramFetcher->get('search')) as $key => $word) {
                if (!$word) {
                    continue;
                }

                $orx = $qb->expr()->orX();
                $orx
                    ->add($qb->expr()->like('s.name', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        if ($paramFetcher->get('courses')) {
            $qb
                ->andWhere($qb->expr()->eq('s.courses', $paramFetcher->get('courses')));

        }

        if (!$count) {
            $qb
                ->orderBy('s.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
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
