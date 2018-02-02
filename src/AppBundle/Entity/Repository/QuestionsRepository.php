<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Questions;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * QuestionsRepository.
 */
class QuestionsRepository extends EntityRepository
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
            ->select('
                q.id,
                q.customId,
                q.year,
                q.type,
                q.questionNumber,
                q.imageUrl,
                q.notes as notes_text,
                q.text as text,                
                GROUP_CONCAT(n.id SEPARATOR \',\') as note_ids
            ')
            ->from('AppBundle:Questions', 'q')
            ->leftJoin('q.note', 'n')
            ->where($qb->expr()->in('q.id', $ids))
            ->groupBy('q.id');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return int|Questions[]
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT q.id)
                ');
        } else {
            $qb
                ->select('q');
        }

        $qb
            ->from('AppBundle:Questions', 'q');

        if ($paramFetcher->get('search')) {
            $andXSearch = $qb->expr()->andX();

            foreach (explode(' ', $paramFetcher->get('search')) as $key => $word) {
                if (!$word) {
                    continue;
                }

                $orx = $qb->expr()->orX();
                $orx
                    ->add($qb->expr()->like('q.notes', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        if ($paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('q.user', $paramFetcher->get('user')));
        }

        if ($paramFetcher->get('semesters')) {
            $qb
                ->andWhere($qb->expr()->eq('q.semesters', $paramFetcher->get('semesters')));
        }

        if ($paramFetcher->get('exam_periods')) {
            $qb
                ->andWhere($qb->expr()->eq('q.examPeriods', $paramFetcher->get('exam_periods')));
        }

        if ($paramFetcher->get('sub_courses')) {
            $qb
                ->andWhere($qb->expr()->eq('q.subCourses', $paramFetcher->get('sub_courses')));
        }

        if ($paramFetcher->get('lectors')) {
            $qb
                ->andWhere($qb->expr()->eq('q.lectors', $paramFetcher->get('lectors')));
        }

        if (!$count) {
            $qb
                ->orderBy('q.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
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
