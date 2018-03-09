<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\ImprovementSuggestions;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * ImprovementSuggestionsRepository
 */
class ImprovementSuggestionsRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return ImprovementSuggestions[]|int
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $params = $paramFetcher->getParams();

        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT i.id)
                ');
        } else {
            $qb
                ->select('i');
        }

        $qb
            ->from('AppBundle:ImprovementSuggestions', 'i');

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('i.user', $paramFetcher->get('user')));
        }

        if (!$count) {
            $qb
                ->orderBy('i.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
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
