<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use AppBundle\Entity\UserQuestionAnswerTest;
use Doctrine\ORM\EntityRepository;

/**
 * UserQuestionAnswerTestRepository.
 */
class UserQuestionAnswerTestRepository extends EntityRepository
{
    /**
     * @param $data
     * @param User $user
     * @return UserQuestionAnswerTest[]
     */
    public function getUserQuestionAnswerTests($data, User $user)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb
            ->select('uqat')
            ->from('AppBundle:UserQuestionAnswerTest', 'uqat')
            ->where($qb->expr()->eq('uqat.user', $user->getId()));
            if ($data && is_array($data)) {
                $orX = $qb->expr()->orX();
                foreach ($data as $datum) {
                    $orX->add($qb->expr()->eq('uqat.questionAnswers', $datum));
                }

                $qb->andWhere($orX);
            }

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }
}
