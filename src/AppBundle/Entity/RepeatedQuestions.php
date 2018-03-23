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
 *        @UniqueConstraint(name="unique_repeated",
 *            columns={"questions_repeated_id", "questions_origin_id", "user_id"})
 *    })
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RepeatedQuestionsRepository")
 * @UniqueEntity(
 *     groups={"post_repeated_questions"},
 *     fields={"questionsRepeated", "questionsOrigin", "user"},
 *     errorPath="user, questions"
 * )
 */
class RepeatedQuestions
{
    use TraitTimestampable;
    const POST_REPEATED_QUESTIONS = 'post_repeated_questions';
    const GET_REPEATED_QUESTIONS = 'get_repeated_questions';

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="originQuestions")
     * @Assert\NotBlank(groups={"post_repeated_questions"})
     * @Annotation\Type("AppBundle\Entity\Questions")
     * @Annotation\Groups({
     *     "post_repeated_questions", "get_repeated_questions"
     * })
     */
    private $questionsOrigin;

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
    private $questionsRepeated;

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

    public static function getPostGroup()
    {
        return [self::POST_REPEATED_QUESTIONS];
    }

    public static function getGetGroup()
    {
        return [self::GET_REPEATED_QUESTIONS];
    }

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
     * Set questionsOrigin.
     *
     * @param null|\AppBundle\Entity\Questions $questionsOrigin
     *
     * @return RepeatedQuestions
     */
    public function setQuestionsOrigin(\AppBundle\Entity\Questions $questionsOrigin = null)
    {
        $this->questionsOrigin = $questionsOrigin;

        return $this;
    }

    /**
     * Get questionsOrigin.
     *
     * @return null|\AppBundle\Entity\Questions
     */
    public function getQuestionsOrigin()
    {
        return $this->questionsOrigin;
    }

    /**
     * Set questionsRepeated.
     *
     * @param null|\AppBundle\Entity\Questions $questionsRepeated
     *
     * @return RepeatedQuestions
     */
    public function setQuestionsRepeated(\AppBundle\Entity\Questions $questionsRepeated = null)
    {
        $this->questionsRepeated = $questionsRepeated;

        return $this;
    }

    /**
     * Get questionsRepeated.
     *
     * @return null|\AppBundle\Entity\Questions
     */
    public function getQuestionsRepeated()
    {
        return $this->questionsRepeated;
    }

    /**
     * Set user.
     *
     * @param null|\AppBundle\Entity\User $user
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
     * @return null|\AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
