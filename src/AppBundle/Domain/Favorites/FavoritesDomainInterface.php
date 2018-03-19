<?php

namespace AppBundle\Domain\Favorites;

use AppBundle\Model\Request\FavoritesRequestModel;

interface FavoritesDomainInterface
{
    /**
     * @param FavoritesRequestModel $model
     *
     * @return mixed
     */
    public function deletedFavoritesByCourses(FavoritesRequestModel $model);
}
