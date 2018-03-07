<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_question_answer_open")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserQuestionAnswerOpenRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class UserQuestionAnswerOpen
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userQuestionAnswerOpen")
     */
    private $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var Questions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="questionAnswersOpen")
     */
    private $questions;

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
     * Set text.
     *
     * @param string $text
     *
     * @return UserQuestionAnswerOpen
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserQuestionAnswerOpen
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
     * Set questions.
     *
     * @param null|\AppBundle\Entity\Questions $questions
     *
     * @return UserQuestionAnswerOpen
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
}
