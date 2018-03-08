<?php

namespace AppBundle\Model\Request;

use AppBundle\Entity\UserQuestionAnswerTest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation;

class UserQuestionAnswerTestRequestModel
{
    /**
     * @var ArrayCollection|UserQuestionAnswerTest[]
     *
     * @Assert\Valid
     * @Annotation\Groups({
     *     "post_user_question_answer_test", "get_user_question_answer_test"
     * })
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\UserQuestionAnswerTest>")
     */
    private $answers;

    /**
     * @return UserQuestionAnswerTest[]|ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers ? $this->answers : new ArrayCollection();
    }

    /**
     * @param UserQuestionAnswerTest[]|ArrayCollection $answers
     * @return $this
     */
    public function setAnswers($answers)
    {
        $this->getAnswers()->clear();
        foreach ($answers as $answer) {
            $this->addAnswers($answer);
        }

        return $this;
    }

    public function addAnswers(UserQuestionAnswerTest $answerTest)
    {
        if (!$this->getAnswers()->contains($answerTest)) {
            $this->answers[]=$answerTest;
        }
    }
}