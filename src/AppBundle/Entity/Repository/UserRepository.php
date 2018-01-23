<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * UserRepository.
 */
class UserRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return int|User[]
     */
    public function getUsersByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT u.id)
                ');
        } else {
            $qb
                ->select('u');
        }

        $qb
            ->from('AppBundle:User', 'u');

        if ($paramFetcher->get('search')) {
            $andXSearch = $qb->expr()->andX();

            foreach (explode(' ', $paramFetcher->get('search')) as $key => $word) {
                if (!$word) {
                    continue;
                }

                $orx = $qb->expr()->orX();
                $orx
                    ->add($qb->expr()->like('u.firstName', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('u.lastName', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('u.username', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('u.email', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        if (!$count) {
            $qb
                ->orderBy('u.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
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
