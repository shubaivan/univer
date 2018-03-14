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
     * @param array $ids
     *
     * @return array
     */
    public function getEntitiesByIds(array $ids)
    {
        if (!$ids) {
            return [];
        }
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb
            ->select('s')
            ->from('AppBundle:SubCourses', 's')
            ->where($qb->expr()->in('s.id', $ids));

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return int|SubCourses[]
     */
    public function getEntitiesByParamsRelation(ParamFetcher $paramFetcher, $count = false)
    {
        $params = $paramFetcher->getParams();
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT s.id)
                ');
        } else {
            $qb
                ->select('
                    s.id as sub_courses_id,
                    s.name as sub_courses_name,
                    GROUP_CONCAT(q.id SEPARATOR \',\') as question_ids                  
                ');
        }

        $qb
            ->from('AppBundle:SubCourses', 's')
            ->innerJoin('s.questions', 'q')
            ->innerJoin('q.note', 'n');

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

        if (array_key_exists('sub_courses', $params) && $paramFetcher->get('sub_courses')) {
            $qb
                ->andWhere($qb->expr()->eq('q.subCourses', $paramFetcher->get('sub_courses')));
        }

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('n.user', $paramFetcher->get('user')));
        }

        if (array_key_exists('courses', $params) && $paramFetcher->get('courses')) {
            $qb
                ->andWhere($qb->expr()->eq('q.courses', $paramFetcher->get('courses')));
        }

        if (!$count) {
            $qb
                ->orderBy('s.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'));
        }

        if ($count) {
            $query = $qb->getQuery();
            $results = $query->getSingleScalarResult();
        } else {
            $qb
                ->groupBy('sub_courses_id');
            $query = $qb->getQuery();
            $results = $query->getResult();
        }

        return $results;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return int|SubCourses[]
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $params = $paramFetcher->getParams();
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT s.id)
                ');
        } else {
            $qb
                ->select('
                    s                  
                ');
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

        if (array_key_exists('courses', $params) && $paramFetcher->get('courses')) {
            $qb
                ->andWhere($qb->expr()->eq('s.courses', $paramFetcher->get('courses')));
        }

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('n.user', $paramFetcher->get('user')));
        }

        if (!$count) {
            $qb
                ->orderBy('s.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'));
        }

        if ($count) {
            $query = $qb->getQuery();
            $results = $query->getSingleScalarResult();
        } else {
            $query = $qb->getQuery();
            $results = $query->getResult();
        }

        return $results;
    }
}
