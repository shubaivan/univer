<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Enum\QuestionsTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="question_corrections")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\QuestionCorrectionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class QuestionCorrections
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_questions_corrections", "get_question"
     * })
     */
    private $id;

    /**
     * @ORM\Column(name="custom_id", type="text", length=255, nullable=true)
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections",
     *     "put_question_corrections"
     * })
     */
    private $customId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="questionCorrections")
     * @Assert\NotBlank(groups={"post_question_corrections", "put_question_corrections"})
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections",
     *     "put_question_corrections"
     * })
     */
    private $year;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"post_course", "put_course"})
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections",
     *     "put_question_corrections"
     * })
     * @Annotation\Accessor(setter="setSerializedAccessorType")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="question_number", type="integer", nullable=true)
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections",
     *     "put_question_corrections"
     * })
     */
    private $questionNumber;

    /**
     * @var string
     * @ORM\Column(name="image_url", type="string", length=255, options={"fixed" = true}, nullable=true)
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections",
     *     "put_question_corrections"
     * })
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *      "post_question_corrections", "get_questions_corrections", "get_question_corrections",
     *      "put_question_corrections"
     * })
     */
    private $notes;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *    "post_question_corrections", "get_questions_corrections", "get_question_corrections",
     *    "put_question_corrections"
     * })
     */
    private $text;

    /**
     * @var ArrayCollection|QuestionAnswersCorrections[]
     *
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\QuestionAnswersCorrections", mappedBy="questionCorrections", cascade={"persist" , "remove"})
     * @Assert\Valid
     * @Annotation\Groups({
     *     "post_question_corrections", "get_question_corrections", "get_questions_corrections", "put_question_corrections"
     * })
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\QuestionAnswersCorrections>")
     * @Annotation\Accessor(setter="setAccessorQuestionAnswersCorrections")
     */
    private $questionAnswersCorrections;

    /**
     * @var Questions
     *
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="questionCorrections")
     * @Assert\NotBlank(groups={"post_question_corrections", "put_question_corrections"})
     * @Annotation\Type("AppBundle\Entity\Questions")
     * @Annotation\Groups({
     *    "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $questions;

    /**
     * @var Semesters
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Semesters", inversedBy="questionCorrections")
     * @Assert\NotBlank(groups={"post_question_corrections"})
     * @Annotation\Type("AppBundle\Entity\Semesters")
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $semesters;

    /**
     * @var ExamPeriods
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ExamPeriods", inversedBy="questionCorrections")
     * @Assert\NotBlank(groups={"post_question_corrections"})
     * @Annotation\Type("AppBundle\Entity\ExamPeriods")
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $examPeriods;

    /**
     * @var SubCourses
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubCourses", inversedBy="questionCorrections")
     * @Assert\NotBlank(groups={"post_question_corrections"})
     * @Annotation\Type("AppBundle\Entity\SubCourses")
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     *
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $subCourses;

    /**
     * @var Lectors
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lectors", inversedBy="questionCorrections")
     * @Assert\NotBlank(groups={"post_question_corrections"})
     * @Annotation\Type("AppBundle\Entity\Lectors")
     * @Annotation\Groups({
     *      "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $lectors;

    /**
     * @var CoursesOfStudy
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CoursesOfStudy", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question_corrections"})
     * @Annotation\Type("AppBundle\Entity\CoursesOfStudy")
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $coursesOfStudy;

    /**
     * @var Courses
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Courses", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question_corrections"})
     * @Annotation\Type("AppBundle\Entity\Courses")
     * @Annotation\Groups({
     *     "post_question_corrections", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $courses;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->questionAnswersCorrections = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set customId.
     *
     * @param null|string $customId
     *
     * @return QuestionCorrections
     */
    public function setCustomId($customId = null)
    {
        $this->customId = $customId;

        return $this;
    }

    /**
     * Get customId.
     *
     * @return null|string
     */
    public function getCustomId()
    {
        return $this->customId;
    }

    /**
     * Set year.
     *
     * @param null|int $year
     *
     * @return QuestionCorrections
     */
    public function setYear($year = null)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return null|int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return QuestionCorrections
     */
    public function setType($type)
    {
        if (!in_array($type, QuestionsTypeEnum::getAvailableTypes(), true)) {
            throw new \InvalidArgumentException(
                'Invalid type. Available type: '.implode(',', QuestionsTypeEnum::getAvailableTypes())
            );
        }

        $this->type = $type;

        return $this;
    }

    public function setSerializedAccessorType($type)
    {
        $this->setType($type);
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set questionNumber.
     *
     * @param null|int $questionNumber
     *
     * @return QuestionCorrections
     */
    public function setQuestionNumber($questionNumber = null)
    {
        $this->questionNumber = $questionNumber;

        return $this;
    }

    /**
     * Get questionNumber.
     *
     * @return null|int
     */
    public function getQuestionNumber()
    {
        return $this->questionNumber;
    }

    /**
     * Set imageUrl.
     *
     * @param null|string $imageUrl
     *
     * @return QuestionCorrections
     */
    public function setImageUrl($imageUrl = null)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl.
     *
     * @return null|string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set notes.
     *
     * @param null|string $notes
     *
     * @return QuestionCorrections
     */
    public function setNotes($notes = null)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return null|string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set text.
     *
     * @param null|string $text
     *
     * @return QuestionCorrections
     */
    public function setText($text = null)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return null|string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set user.
     *
     * @param null|\AppBundle\Entity\User $user
     *
     * @return QuestionCorrections
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

    /**
     * Add questionAnswersCorrection.
     *
     * @param \AppBundle\Entity\QuestionAnswersCorrections $questionAnswersCorrection
     *
     * @return QuestionCorrections
     */
    public function addQuestionAnswersCorrection(\AppBundle\Entity\QuestionAnswersCorrections $questionAnswersCorrection)
    {
        $this->questionAnswersCorrections[] = $questionAnswersCorrection;

        return $this;
    }

    /**
     * Remove questionAnswersCorrection.
     *
     * @param \AppBundle\Entity\QuestionAnswersCorrections $questionAnswersCorrection
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeQuestionAnswersCorrection(\AppBundle\Entity\QuestionAnswersCorrections $questionAnswersCorrection)
    {
        return $this->questionAnswersCorrections->removeElement($questionAnswersCorrection);
    }

    /**
     * Get questionAnswersCorrections.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionAnswersCorrections()
    {
        return $this->questionAnswersCorrections ? $this->questionAnswersCorrections : new ArrayCollection();
    }

    /**
     * @param $questionAnswers
     */
    public function setAccessorQuestionAnswersCorrections($questionAnswers)
    {
        $this->getQuestionAnswersCorrections()->clear();
        foreach ($questionAnswers as $questionAnswer) {
            $this->addQuestionAnswersCorrections($questionAnswer);
        }
    }

    /**
     * Add questionAnswerCorrections.
     *
     * @param \AppBundle\Entity\QuestionAnswersCorrections $questionAnswersCorrections
     *
     * @return QuestionCorrections
     */
    public function addQuestionAnswersCorrections(\AppBundle\Entity\QuestionAnswersCorrections $questionAnswersCorrections)
    {
        $this->questionAnswersCorrections[] = $questionAnswersCorrections;
        $questionAnswersCorrections->setQuestionCorrections($this);

        return $this;
    }

    /**
     * Set questions.
     *
     * @param null|\AppBundle\Entity\Questions $questions
     *
     * @return QuestionCorrections
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
     * Set semesters.
     *
     * @param null|\AppBundle\Entity\Semesters $semesters
     *
     * @return QuestionCorrections
     */
    public function setSemesters(\AppBundle\Entity\Semesters $semesters = null)
    {
        $this->semesters = $semesters;

        return $this;
    }

    /**
     * Get semesters.
     *
     * @return null|\AppBundle\Entity\Semesters
     */
    public function getSemesters()
    {
        return $this->semesters;
    }

    /**
     * Set examPeriods.
     *
     * @param null|\AppBundle\Entity\ExamPeriods $examPeriods
     *
     * @return QuestionCorrections
     */
    public function setExamPeriods(\AppBundle\Entity\ExamPeriods $examPeriods = null)
    {
        $this->examPeriods = $examPeriods;

        return $this;
    }

    /**
     * Get examPeriods.
     *
     * @return null|\AppBundle\Entity\ExamPeriods
     */
    public function getExamPeriods()
    {
        return $this->examPeriods;
    }

    /**
     * Set subCourses.
     *
     * @param null|\AppBundle\Entity\SubCourses $subCourses
     *
     * @return QuestionCorrections
     */
    public function setSubCourses(\AppBundle\Entity\SubCourses $subCourses = null)
    {
        $this->subCourses = $subCourses;

        return $this;
    }

    /**
     * Get subCourses.
     *
     * @return null|\AppBundle\Entity\SubCourses
     */
    public function getSubCourses()
    {
        return $this->subCourses;
    }

    /**
     * Set lectors.
     *
     * @param null|\AppBundle\Entity\Lectors $lectors
     *
     * @return QuestionCorrections
     */
    public function setLectors(\AppBundle\Entity\Lectors $lectors = null)
    {
        $this->lectors = $lectors;

        return $this;
    }

    /**
     * Get lectors.
     *
     * @return null|\AppBundle\Entity\Lectors
     */
    public function getLectors()
    {
        return $this->lectors;
    }

    /**
     * Set coursesOfStudy.
     *
     * @param null|\AppBundle\Entity\CoursesOfStudy $coursesOfStudy
     *
     * @return QuestionCorrections
     */
    public function setCoursesOfStudy(\AppBundle\Entity\CoursesOfStudy $coursesOfStudy = null)
    {
        $this->coursesOfStudy = $coursesOfStudy;

        return $this;
    }

    /**
     * Get coursesOfStudy.
     *
     * @return null|\AppBundle\Entity\CoursesOfStudy
     */
    public function getCoursesOfStudy()
    {
        return $this->coursesOfStudy;
    }

    /**
     * Set courses.
     *
     * @param null|\AppBundle\Entity\Courses $courses
     *
     * @return QuestionCorrections
     */
    public function setCourses(\AppBundle\Entity\Courses $courses = null)
    {
        $this->courses = $courses;

        return $this;
    }

    /**
     * Get courses.
     *
     * @return null|\AppBundle\Entity\Courses
     */
    public function getCourses()
    {
        return $this->courses;
    }
}
