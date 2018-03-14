<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="question_answers_corrections")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\QuestionAnswersCorrectionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class QuestionAnswersCorrections
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_questions_corrections", "post_question_corrections", "get_question_corrections"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=65000, nullable=false)
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Groups({
     *     "get_questions_corrections", "post_question_corrections", "get_question_corrections"
     * })
     */
    private $answer;

    /**
     * @ORM\Column(name="is_true", type="boolean", nullable=false)
     * @Annotation\Groups({
     *     "get_questions_corrections", "post_question_corrections", "get_question_corrections"
     * })
     */
    private $isTrue = false;

    /**
     * @ORM\Column(name="point_eng", type="string", length=10, options={"fixed" = true}, nullable=true)
     * @Annotation\Groups({
     *     "get_questions_corrections", "post_question_corrections", "get_question_corrections"
     * })
     */
    private $pointEng;

    /**
     * @ORM\Column(name="point_heb", type="string", length=10, options={"fixed" = true}, nullable=true)
     * @Annotation\Groups({
     *     "get_questions_corrections", "post_question_corrections", "get_question_corrections"
     * })
     */
    private $pointHeb;

    /**
     * @var QuestionCorrections
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\QuestionCorrections", inversedBy="questionAnswersCorrections")
     * @Assert\NotBlank(groups={"get_questions_corrections", "post_question_corrections", "get_question_corrections"})
     */
    private $questionCorrections;

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
     * @return QuestionAnswersCorrections
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
     * @return QuestionAnswersCorrections
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
     * @param null|string $pointEng
     *
     * @return QuestionAnswersCorrections
     */
    public function setPointEng($pointEng = null)
    {
        $this->pointEng = $pointEng;

        return $this;
    }

    /**
     * Get pointEng.
     *
     * @return null|string
     */
    public function getPointEng()
    {
        return $this->pointEng;
    }

    /**
     * Set pointHeb.
     *
     * @param null|string $pointHeb
     *
     * @return QuestionAnswersCorrections
     */
    public function setPointHeb($pointHeb = null)
    {
        $this->pointHeb = $pointHeb;

        return $this;
    }

    /**
     * Get pointHeb.
     *
     * @return null|string
     */
    public function getPointHeb()
    {
        return $this->pointHeb;
    }

    /**
     * Set questionCorrections.
     *
     * @param null|\AppBundle\Entity\QuestionCorrections $questionCorrections
     *
     * @return QuestionAnswersCorrections
     */
    public function setQuestionCorrections(\AppBundle\Entity\QuestionCorrections $questionCorrections = null)
    {
        $this->questionCorrections = $questionCorrections;

        return $this;
    }

    /**
     * Get questionCorrections.
     *
     * @return null|\AppBundle\Entity\QuestionCorrections
     */
    public function getQuestionCorrections()
    {
        return $this->questionCorrections;
    }
}
