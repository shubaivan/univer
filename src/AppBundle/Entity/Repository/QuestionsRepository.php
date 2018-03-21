<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Enum\EventStateEnum;
use AppBundle\Entity\Questions;
use AppBundle\Helper\AdditionalFunction;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * QuestionsRepository.
 */
class QuestionsRepository extends EntityRepository
{
    /**
     * @var AdditionalFunction
     */
    private $additionalFunction;

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
     * @param $data
     * @param $id
     */
    public function updateNumber($data, $id)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb
            ->update('AppBundle:Questions', 'q')
            ->set('q.questionNumber', $qb->expr()->literal($data))
            ->where('q.id = :id')
            ->setParameter('id', $id);

        $qb->getQuery()->execute();
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
     * @return int|Questions[]
     */
    public function getEntitiesForProcessing()
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb
            ->select('q')
            ->from('AppBundle:Questions', 'q')
            ->where(
                $qb->expr()
                ->andX(
                    $qb->expr()->isNotNull('q.votesAt'),
                    $qb->expr()->lte('q.votesAt', ':dt')
                    )
            )
            ->setParameter(':dt', (new \DateTime())->format('Y-m-d H:i:s'));

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @param ParameterBag $parameterBag
     * @param bool         $count
     *
     * @return int|Questions[]
     */
    public function getEntitiesByParams(ParameterBag $parameterBag, $count = false)
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
                ->select('
                    q
                ');
        }

        $qb
            ->from('AppBundle:Questions', 'q');

        if ($parameterBag->get('search')) {
            $andXSearch = $qb->expr()->andX();

            foreach (explode(' ', $parameterBag->get('search')) as $key => $word) {
                if (!$word) {
                    continue;
                }

                $orx = $qb->expr()->orX();
                $orx
                    ->add($qb->expr()->like('q.questionNumber', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('q.notes', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('q.text', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        if (null !== $parameterBag->get('votes')) {
            $qbIncludedVotesResult = $em->createQueryBuilder();
            $qbIncludedVotesResult
                ->select('IDENTITY(votes.questions)')
                ->from('AppBundle:Votes', 'votes');

            if (true === $parameterBag->get('votes')) {
                $qb
                    ->andWhere($qb->expr()->in('q.id', $qbIncludedVotesResult->getDQL()));
            } elseif (false === $parameterBag->get('votes')) {
                $qb
                    ->andWhere($qb->expr()->notIn('q.id', $qbIncludedVotesResult->getDQL()));
            }
        }

        if ($parameterBag->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('q.user', $parameterBag->get('user')));
        }

        if ($parameterBag->get('semesters')) {
            $this->queryAndXHelper($qb, $parameterBag, 'semesters', 'semesters');
        }

        if ($parameterBag->get('exam_periods')) {
            $this->queryAndXHelper($qb, $parameterBag, 'exam_periods', 'examPeriods');
        }

        if ($parameterBag->get('sub_courses')) {
            $this->queryAndXHelper($qb, $parameterBag, 'sub_courses', 'subCourses');
        }

        if ($parameterBag->get('lectors')) {
            $this->queryAndXHelper($qb, $parameterBag, 'lectors', 'lectors');
        }

        if ($parameterBag->get('courses')) {
            $orXSearch = $qb->expr()->orX();

            foreach ($parameterBag->get('courses') as $key => $id) {
                if (!$id) {
                    continue;
                }
                $orXSearch
                    ->add($qb->expr()->eq('q.courses', $id));
            }
            $qb->andWhere($orXSearch);
        }

        if ($parameterBag->get('years')) {
            $orXSearch = $qb->expr()->orX();

            foreach ($parameterBag->get('years') as $key => $id) {
                if (!$id) {
                    continue;
                }

                $orXSearch
                    ->add($qb->expr()->eq(
                        'q.year',
                        ':x'.$key.'year'
                    ));

                $qb
                    ->setParameter('x'.$key.'year', $id);
            }
            $qb->andWhere($orXSearch);
        }

        if ($parameterBag->get('courses_of_study')) {
            $qb
                ->leftJoin('q.coursesOfStudy', 'courses_of_study')
                ->andWhere('courses_of_study.id = :courses_of_study_id')
                ->setParameter('courses_of_study_id', $parameterBag->get('courses_of_study'));
        }

        if ($parameterBag->get('repeated') && true === $parameterBag->get('repeated')) {
            $qbIncludedRepeatResult = $em->createQueryBuilder();
            $qbIncludedRepeatResult
                ->select('IDENTITY(rq.questions)')
                ->from('AppBundle:RepeatedQuestions', 'rq')
                ->andWhere($qbIncludedRepeatResult->expr()->eq('rq.user', $parameterBag->get('user')));

            $qb
                ->andWhere($qb->expr()->in('q.id', $qbIncludedRepeatResult->getDQL()));
        }

        if ($parameterBag->get('user_state')) {
            if (EventStateEnum::UNRESOLVED === $parameterBag->get('user_state')) {
                $andX = $qb->expr()->andX();

                $qbExcludedTest = $em->createQueryBuilder();
                $qbExcludedTest
                    ->select('IDENTITY(qa.questions)')
                    ->from('AppBundle:UserQuestionAnswerTest', 'uqat')
                    ->leftJoin('uqat.questionAnswers', 'qa')
                    ->where($qbExcludedTest->expr()->eq('uqat.user', $parameterBag->get('user')))
                    ->groupBy('qa.questions');

                $andX->add($qb->expr()->notIn('q.id', $qbExcludedTest->getDQL()));

                $qbExcludedOpen = $em->createQueryBuilder();
                $qbExcludedOpen
                    ->select('IDENTITY(uqao.questions)')
                    ->from('AppBundle:UserQuestionAnswerOpen', 'uqao')
                    ->where($qbExcludedOpen->expr()->eq('uqao.user', $parameterBag->get('user')));

                $andX->add($qb->expr()->notIn('q.id', $qbExcludedOpen->getDQL()));

                $qb->andWhere($andX);
            } elseif (EventStateEnum::NOT_SUCCESSED === $parameterBag->get('user_state')) {
                $qbExcludedResult = $em->createQueryBuilder();
                $qbExcludedResult
                    ->select('IDENTITY(uqar.questions)')
                    ->from('AppBundle:UserQuestionAnswerResult', 'uqar')
                    ->where('uqar.result = :uqar_result')
                    ->setParameter('uqar_result', false)
                    ->andWhere($qbExcludedResult->expr()->eq('uqar.user', $parameterBag->get('user')));

                $qb
                    ->setParameter('uqar_result', false)
                    ->andWhere($qb->expr()->in('q.id', $qbExcludedResult->getDQL()));
            }
        }

        if (!$count) {
            $qb
                ->orderBy('q.'.$parameterBag->get('sort_by'), $parameterBag->get('sort_order'))
                ->setFirstResult($parameterBag->get('count') * ($parameterBag->get('page') - 1))
                ->setMaxResults($parameterBag->get('count'));
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
     * @param QueryBuilder $qb
     * @param ParameterBag $parameterBag
     * @param $paramKey
     * @param $mappingName
     */
    private function queryAndXHelper(
        QueryBuilder $qb,
        ParameterBag $parameterBag,
        $paramKey,
        $mappingName
    ) {
        $orXSearch = $qb->expr()->orX();

        foreach ($parameterBag->get($paramKey) as $key => $id) {
            if (!$id) {
                continue;
            }

            $orXSearch
                ->add($qb->expr()->eq('q.'.$mappingName, $id));
        }
        $qb->andWhere($orXSearch);
    }
}
