<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Comments;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * CommentsRepository.
 */
class CommentsRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return Comments[]|int
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT c.id)
                ');
        } else {
            $qb
                ->select('c');
        }

        $qb
            ->from('AppBundle:Comments', 'c');

        if ($paramFetcher->get('search')) {
            $andXSearch = $qb->expr()->andX();

            foreach (explode(' ', $paramFetcher->get('search')) as $key => $word) {
                if (!$word) {
                    continue;
                }

                $orx = $qb->expr()->orX();
                $orx
                    ->add($qb->expr()->like('c.text', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        $params = $paramFetcher->getParams();

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('c.user', $paramFetcher->get('user')));
        }

        if (array_key_exists('questions', $params) && $paramFetcher->get('questions')) {
            $qb
                ->andWhere($qb->expr()->eq('c.questions', $paramFetcher->get('questions')));
        }

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('c.user', $paramFetcher->get('user')));
        }

        if (array_key_exists('year', $params) && $paramFetcher->get('year')) {

            $date = $this->validateDateTime($paramFetcher->get('year'), 'Y');
            $first = clone $date;
            $first->setDate($date->format('Y'), 1, 1);
            $first->setTime(0, 0, 0);
            $last = clone $date;
            $last->setDate($date->format('Y'), 12, 31);
            $last->setTime(23, 59   , 59);

            $qb
                ->andWhere($qb->expr()->between('c.createdAt', ':dateFrom', ':dateTo'))
                ->setParameter('dateFrom', $first->format('Y-m-d H:i:s'))
                ->setParameter('dateTo', $last->format('Y-m-d H:i:s'));
        }

        if (!$count) {
            $qb
                ->orderBy('c.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
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
     * @param $date
     *
     * @throws \Exception
     *
     * @return \DateTime|false
     */
    public function validateDateTime($date, $format)
    {
        $checkResult = false;
        $dateTimeClass = \DateTime::createFromFormat($format, $date);
        if ($dateTimeClass) {
            $checkResult = $dateTimeClass->format($format) == $date;
        }

        if (!$dateTimeClass instanceof \DateTime || !$checkResult) {
            throw new \Exception('Date fields must be format \'dd.mm.yy\'');
        }

        return $dateTimeClass;
    }
}
