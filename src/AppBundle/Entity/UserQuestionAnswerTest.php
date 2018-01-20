<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

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
     */
    private $id;

    /**
     * @var QuestionAnswers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\QuestionAnswers", inversedBy="userQuestionAnswerTest")
     */
    private $questionAnswers;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userQuestionAnswerTest")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $result;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set result
     *
     * @param boolean $result
     *
     * @return UserQuestionAnswerTest
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return boolean
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set questionAnswers
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
     * Get questionAnswers
     *
     * @return \AppBundle\Entity\QuestionAnswers
     */
    public function getQuestionAnswers()
    {
        return $this->questionAnswers;
    }

    /**
     * Set user
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
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
