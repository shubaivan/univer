<?php

namespace AppBundle\Domain\Favorites;

use AppBundle\Entity\Notifications;
use AppBundle\Entity\Repository\FavoritesRepository;
use AppBundle\Entity\Repository\NotificationsRepository;
use AppBundle\Model\Request\FavoritesRequestModel;
use AppBundle\Services\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class FavoritesDomain implements FavoritesDomainInterface
{
    /**
     * @var FavoritesRepository
     */
    private $favoritesRepository;

    /**
     * FavoritesDomain constructor.
     * @param FavoritesRepository $favoritesRepository
     */
    public function __construct(
        FavoritesRepository $favoritesRepository
    ) {
        $this->favoritesRepository = $favoritesRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function deletedFavoritesByCourses(FavoritesRequestModel $model)
    {
        $this->getFavoritesRepository()
            ->deletedFavorites($this->getFavoritesRepository()
            ->getEntitiesForRemove($model));
    }

    /**
     * @return FavoritesRepository
     */
    private function getFavoritesRepository()
    {
        return $this->favoritesRepository;
    }
}
