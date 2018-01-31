<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Notes;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * NotesRepository.
 */
class NotesRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return int|Notes[]
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT n.id)
                ');
        } else {
            $qb
                ->select('n');
        }

        $qb
            ->from('AppBundle:Notes', 'n')
            ->innerJoin('n.questions', 'q');

        if ($paramFetcher->get('search')) {
            $andXSearch = $qb->expr()->andX();

            foreach (explode(' ', $paramFetcher->get('search')) as $key => $word) {
                if (!$word) {
                    continue;
                }

                $orx = $qb->expr()->orX();
                $orx
                    ->add($qb->expr()->like('n.text', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        $params = $paramFetcher->getParams();

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('n.user', $paramFetcher->get('user')));
        }

        if (array_key_exists('admin', $params) && $paramFetcher->get('admin')) {
            $qb
                ->andWhere($qb->expr()->eq('n.admin', $paramFetcher->get('admin')));
        }

        if ($paramFetcher->get('questions')) {
            $qb
                ->andWhere($qb->expr()->eq('n.questions', $paramFetcher->get('questions')));
        }

        if (!$count) {
            $qb
//                ->orderBy('n.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'));
        }

        if ($count) {
            $query = $qb->getQuery();
            $results = $query->getSingleScalarResult();
        } else {
            $qb->orderBy('q.subCourses');
            $query = $qb->getQuery();
            $results = $query->getResult();
        }

        return $results;
    }
}
