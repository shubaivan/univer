<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="auestion_answers")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\QuestionAnswersRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class QuestionAnswers
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=65000, nullable=true)
     */
    private $answer;

    /**
     * @ORM\Column(name="is_true", type="boolean", nullable=true)
     */
    private $isTrue;

    /**
     * @ORM\Column(name="point_eng", type="string", length=10, options={"fixed" = true})
     */
    private $pointEng;

    /**
     * @ORM\Column(name="point_heb", type="string", length=10, options={"fixed" = true})
     */
    private $pointHeb;

    /**
     * @var ArrayCollection|UserQuestionAnswerTest[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerTest", mappedBy="questionAnswers", cascade={"persist"})
     */
    private $userQuestionAnswerTest;

    /**
     * @var Questions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="note")
     */
    private $questions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->userQuestionAnswerTest = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set answer.
     *
     * @param string $answer
     *
     * @return QuestionAnswers
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set isTrue.
     *
     * @param bool $isTrue
     *
     * @return QuestionAnswers
     */
    public function setIsTrue($isTrue)
    {
        $this->isTrue = $isTrue;

        return $this;
    }

    /**
     * Get isTrue.
     *
     * @return bool
     */
    public function getIsTrue()
    {
        return $this->isTrue;
    }

    /**
     * Set pointEng.
     *
     * @param string $pointEng
     *
     * @return QuestionAnswers
     */
    public function setPointEng($pointEng)
    {
        $this->pointEng = $pointEng;

        return $this;
    }

    /**
     * Get pointEng.
     *
     * @return string
     */
    public function getPointEng()
    {
        return $this->pointEng;
    }

    /**
     * Set pointHeb.
     *
     * @param string $pointHeb
     *
     * @return QuestionAnswers
     */
    public function setPointHeb($pointHeb)
    {
        $this->pointHeb = $pointHeb;

        return $this;
    }

    /**
     * Get pointHeb.
     *
     * @return string
     */
    public function getPointHeb()
    {
        return $this->pointHeb;
    }

    /**
     * Add userQuestionAnswerTest.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest
     *
     * @return QuestionAnswers
     */
    public function addUserQuestionAnswerTest(\AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest)
    {
        $this->userQuestionAnswerTest[] = $userQuestionAnswerTest;

        return $this;
    }

    /**
     * Remove userQuestionAnswerTest.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest
     */
    public function removeUserQuestionAnswerTest(\AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest)
    {
        $this->userQuestionAnswerTest->removeElement($userQuestionAnswerTest);
    }

    /**
     * Get userQuestionAnswerTest.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserQuestionAnswerTest()
    {
        return $this->userQuestionAnswerTest;
    }

    /**
     * Set questions.
     *
     * @param \AppBundle\Entity\Questions $questions
     *
     * @return QuestionAnswers
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
}
