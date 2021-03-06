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
 *
 * @ORM\Table(name="user_question_answer_result",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="answer_unique",
 *            columns={"questions_id", "user_id"})
 *    }
 * )
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserQuestionAnswerResultRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @UniqueEntity(
 *     groups={"post_user_question_answer_test"},
 *     fields={"questions", "user"}
 * )
 */
class UserQuestionAnswerResult
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_user_question_answer_test"
     * })
     */
    private $id;

    /**
     * @var Questions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="userQuestionAnswerResult")
     * @Assert\NotBlank(groups={"post_user_question_answer_test"})
     * @Annotation\Groups({
     *     "post_user_question_answer_test", "get_user_question_answer_test"
     * })
     * @Annotation\Type("AppBundle\Entity\Questions")
     */
    private $questions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userQuestionAnswerResult")
     * @Assert\NotBlank(groups={"post_user_question_answer_test"})
     * @Annotation\Groups({
     *     "post_user_question_answer_test", "get_user_question_answer_test"
     * })
     * @Annotation\Type("AppBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotNull(groups={"post_user_question_answer_test"})
     * @Annotation\Groups({
     *     "post_user_question_answer_test", "get_user_question_answer_test"
     * })
     */
    private $result = false;

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
     * Set result.
     *
     * @param bool $result
     *
     * @return UserQuestionAnswerResult
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result.
     *
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set questions.
     *
     * @param null|\AppBundle\Entity\Questions $questions
     *
     * @return UserQuestionAnswerResult
     */
    public function setQuestions(\AppBundle\Entity\Questions $questions = null)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions.
     *
     * @return null|\AppBundle\Entity\Questions
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set user.
     *
     * @param null|\AppBundle\Entity\User $user
     *
     * @return UserQuestionAnswerResult
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
