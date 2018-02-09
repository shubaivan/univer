<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_question_answer_test")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserQuestionAnswerTestRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class UserQuestionAnswerTest
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
     * @var QuestionAnswers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\QuestionAnswers", inversedBy="userQuestionAnswerTest")
     * @Assert\NotBlank(groups={"post_user_question_answer_test"})
     * @Annotation\Groups({
     *     "post_user_question_answer_test", "get_user_question_answer_test"
     * })
     * @Annotation\Type("AppBundle\Entity\QuestionAnswers")
     */
    private $questionAnswers;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userQuestionAnswerTest")
     * @Assert\NotBlank(groups={"post_user_question_answer_test"})
     * @Annotation\Groups({
     *     "post_user_question_answer_test", "get_user_question_answer_test"
     * })
     * @Annotation\Type("AppBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\NotBlank(groups={"post_user_question_answer_test"})
     * @Annotation\Groups({
     *     "post_user_question_answer_test", "get_user_question_answer_test"
     * })
     */
    private $result = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Annotation\Groups({
     *     "get_user_question_answer_test"
     * })
     */
    private $compareResult = false;

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
     * @return UserQuestionAnswerTest
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
     * Set questionAnswers.
     *
     * @param \AppBundle\Entity\QuestionAnswers $questionAnswers
     *
     * @return UserQuestionAnswerTest
     */
    public function setQuestionAnswers(\AppBundle\Entity\QuestionAnswers $questionAnswers = null)
    {
        $this->questionAnswers = $questionAnswers;

        return $this;
    }

    /**
     * Get questionAnswers.
     *
     * @return \AppBundle\Entity\QuestionAnswers
     */
    public function getQuestionAnswers()
    {
        return $this->questionAnswers;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserQuestionAnswerTest
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



    /**
     * Set compareResult.
     *
     * @param bool|null $compareResult
     *
     * @return UserQuestionAnswerTest
     */
    public function setCompareResult($compareResult = null)
    {
        $this->compareResult = $compareResult;

        return $this;
    }

    /**
     * Get compareResult.
     *
     * @return bool|null
     */
    public function getCompareResult()
    {
        return $this->compareResult;
    }

    /**
     * @ORM\PrePersist()
     */
    public function PrePersist()
    {
        $this->compareResult = $this->result == $this->getQuestionAnswers()->getIsTrue();
    }
}
