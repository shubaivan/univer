<?php

namespace AppBundle\Model\Request;

use AppBundle\Entity\Courses;
use AppBundle\Entity\User;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FavoritesRequestModel.
 */
class FavoritesRequestModel
{
    const GROUP_REMOVE = 'remove_favorites';

    /**
     * @var User
     *
     * @Assert\NotBlank(groups={"remove_favorites"})
     * @Annotation\Groups({
     *     "remove_favorites"
     * })
     * @Annotation\Type("AppBundle\Entity\User")
     */
    private $user;

    /**
     * @var Courses
     *
     * @Assert\NotBlank(groups={"remove_favorites"})
     * @Annotation\Groups({
     *     "remove_favorites"
     * })
     * @Annotation\Type("AppBundle\Entity\Courses")
     */
    private $courses;

    /**
     * @return array
     */
    public static function getRemoveGroup()
    {
        return [self::GROUP_REMOVE];
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Courses
     */
    public function getCourses(): Courses
    {
        return $this->courses;
    }
}
