<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="favorites",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="unique_favorites",
 *            columns={"questions_id", "user_id"})
 *    })
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\FavoritesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @UniqueEntity(
 *     groups={"post_favorite", "put_favorite"},
 *     fields={"questions", "user"},
 *     errorPath="user, questions",
 *     message="favorite question exist."
 * )
 */
class Favorites
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_favorite", "get_favorites"
     * })
     */
    private $id;

    /**
     * @var Questions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="favorites")
     * @Assert\NotBlank(groups={"post_favorite", "put_favorite"})
     * @Annotation\Type("AppBundle\Entity\Questions")
     * @Annotation\Groups({
     *     "get_favorite", "get_favorites", "post_favorite", "put_favorite"
     * })
     */
    private $questions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="favorites")
     * @Assert\NotBlank(groups={"post_favorite", "put_favorite"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "get_favorite", "get_favorites", "post_favorite", "put_favorite"
     * })
     */
    private $user;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set questions.
     *
     * @param \AppBundle\Entity\Questions $questions
     *
     * @return Favorites
     */
    public function setQuestions(\AppBundle\Entity\Questions $questions = null)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions.
     *
     * @return \AppBundle\Entity\Questions
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Favorites
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
