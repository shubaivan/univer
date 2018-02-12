<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Questions;
use AppBundle\Helper\AdditionalFunction;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * QuestionsRepository.
 */
class QuestionsRepository extends EntityRepository
{
    /**
     * @var AdditionalFunction
     */
    private  $additionalFunction;

    /**
     * @DI\InjectParams({
     *     "additionalFunction" = @DI\Inject("app.additional_function"),
     * })
     */
    public function setParam(AdditionalFunction $additionalFunction)
    {
        $this->additionalFunction = $additionalFunction;
    }

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
                    ->add($qb->expr()->like('q.notes', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('q.text', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        if ($paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('q.user', $paramFetcher->get('user')));
        }

        if ($paramFetcher->get('semesters')) {
            $this->queryAndXHelper($qb, $paramFetcher, 'semesters', 'semesters');
        }

        if ($paramFetcher->get('exam_periods')) {
            $this->queryAndXHelper($qb, $paramFetcher, 'exam_periods', 'examPeriods');
        }

        if ($paramFetcher->get('sub_courses')) {
            $this->queryAndXHelper($qb, $paramFetcher, 'sub_courses', 'subCourses');
        }

        if ($paramFetcher->get('lectors')) {
            $this->queryAndXHelper($qb, $paramFetcher, 'lectors', 'lectors');
        }

        if ($paramFetcher->get('courses') || $paramFetcher->get('courses_of_study')) {
            $qb
                ->leftJoin('q.subCourses', 'subCourses')
                ->leftJoin('subCourses.courses', 'courses');
        }

        if ($paramFetcher->get('courses')) {
            $orXSearch = $qb->expr()->orX();
            $semestersData = trim($paramFetcher->get('courses'));
            foreach (explode(',', $semestersData) as $key => $id) {
                if (!$id) {
                    continue;
                }
                $orXSearch
                    ->add($qb->expr()->eq('courses.id', $id));
            }
            $qb->andWhere($orXSearch);
        }

        if ($paramFetcher->get('years')) {

            $orXSearch = $qb->expr()->orX();
            $yearData = trim($paramFetcher->get('years'));
            foreach (explode(',', $yearData) as $key => $id) {
                if (!$id) {
                    continue;
                }
                $date = $this->additionalFunction->validateDateTime($id, 'Y');
                $first = clone $date;
                $first->setDate($date->format('Y'), 1, 1);
                $first->setTime(0, 0, 0);
                $last = clone $date;
                $last->setDate($date->format('Y'), 12, 31);
                $last->setTime(23, 59   , 59);

                $orXSearch
                    ->add($qb->expr()->between(
                        'q.createdAt',
                        $qb->expr()->literal($first->format('Y-m-d H:i:s')),
                        $qb->expr()->literal($last->format('Y-m-d H:i:s')))
                    );
            }
            $qb->andWhere($orXSearch);
        }

        if ($paramFetcher->get('courses_of_study')) {
            $qb
                ->leftJoin('courses.coursesOfStudy', 'courses_of_study')
                ->andWhere('courses_of_study.id = :courses_of_study_id')
                ->setParameter('courses_of_study_id', $paramFetcher->get('courses_of_study'));
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

    private function queryAndXHelper(
        QueryBuilder $qb,
        ParamFetcher $paramFetcher,
        $paramKey,
        $mappingName
    )
    {
        $orXSearch = $qb->expr()->orX();
        $data = trim($paramFetcher->get($paramKey));
        foreach (explode(',', $data) as $key => $id) {
            if (!$id) {
                continue;
            }

            $orXSearch
                ->add($qb->expr()->eq('q.'.$mappingName, $id));
        }
        $qb->andWhere($orXSearch);
    }
}
