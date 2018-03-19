<?php

namespace AppBundle\Domain\Favorites;

use AppBundle\Entity\Repository\FavoritesRepository;
use AppBundle\Model\Request\FavoritesRequestModel;

class FavoritesDomain implements FavoritesDomainInterface
{
    /**
     * @var FavoritesRepository
     */
    private $favoritesRepository;

    /**
     * FavoritesDomain constructor.
     *
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
