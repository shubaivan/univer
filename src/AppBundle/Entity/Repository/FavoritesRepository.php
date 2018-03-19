<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Favorites;
use AppBundle\Model\Request\FavoritesRequestModel;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * FavoritesRepository.
 */
class FavoritesRepository extends EntityRepository
{
    /**
     * @param ParamFetcher $paramFetcher
     * @param bool         $count
     *
     * @return Favorites[]|int
     */
    public function getEntitiesByParams(ParamFetcher $paramFetcher, $count = false)
    {
        $params = $paramFetcher->getParams();

        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        if ($count) {
            $qb
                ->select('
                    COUNT(DISTINCT f.id)
                ');
        } else {
            $qb
                ->select('f');
        }

        $qb
            ->from('AppBundle:Favorites', 'f');

        if ($paramFetcher->get('search')) {
            $qb
                ->innerJoin('f.user', 'u')
                ->innerJoin('f.questions', 'q');
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
                    ->add($qb->expr()->like('u.email', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('q.notes', $qb->expr()->literal('%'.$word.'%')))
                    ->add($qb->expr()->like('q.text', $qb->expr()->literal('%'.$word.'%')));

                $andXSearch->add($orx);
            }

            $qb->andWhere($andXSearch);
        }

        if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
            $qb
                ->andWhere($qb->expr()->eq('f.user', $paramFetcher->get('user')));
        }

        if (!$count) {
            $qb
                ->orderBy('f.'.$paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
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
     * @param FavoritesRequestModel $model
     * @return Favorites[]
     */
    public function getEntitiesForRemove(FavoritesRequestModel $model)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb
            ->select('f');

        $qb
            ->from('AppBundle:Favorites', 'f');

        if ($model->getUser()) {
            $qb
                ->andWhere('f.user = :user')
                ->setParameter('user', $model->getUser());
        }

        if ($model->getCourses()) {
            $qb
                ->leftJoin('f.questions', 'questions')
                ->andWhere('questions.courses = :courses')
                ->setParameter('courses', $model->getCourses());
        }

        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }

    /**
     * @param Favorites[] $favorites
     */
    public function deletedFavorites($favorites = [])
    {
        if (!$favorites) {
            return;
        }
        foreach ($favorites as $favorite) {
            $this->getEntityManager()->remove($favorite);
        }

        $this->getEntityManager()->flush();
    }
}
