<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="repeated_questions",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="unique_favorites",
 *            columns={"questions_id", "user_id"})
 *    })
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RepeatedQuestionsRepository")
 * @UniqueEntity(
 *     groups={"post_repeated_questions"},
 *     fields={"questions", "user"},
 *     errorPath="user, questions"
 * )
 */
class RepeatedQuestions
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_repeated_questions"
     * })
     */
    private $id;

    /**
     * @var Questions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="repeatedQuestions")
     * @Assert\NotBlank(groups={"post_repeated_questions"})
     * @Annotation\Type("AppBundle\Entity\Questions")
     * @Annotation\Groups({
     *     "post_repeated_questions", "get_repeated_questions"
     * })
     */
    private $questions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="repeatedQuestions")
     * @Assert\NotBlank(groups={"post_repeated_questions"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "post_repeated_questions", "get_repeated_questions"
     * })
     */
    private $user;

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"get_repeated_questions"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_repeated_questions"})
     */
    public function getSerializedUpdatedAt()
    {
        return $this->updatedAt;
    }

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
     * @param \AppBundle\Entity\Questions|null $questions
     *
     * @return RepeatedQuestions
     */
    public function setQuestions(\AppBundle\Entity\Questions $questions = null)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions.
     *
     * @return \AppBundle\Entity\Questions|null
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return RepeatedQuestions
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
