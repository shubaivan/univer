<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Enum\QuestionsTypeEnum;
use AppBundle\Validator\Constraints\ConditionAuthor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="questions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\QuestionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ConditionAuthor(groups={"post_question", "put_question"})
 */
class Questions
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "get_note", "get_notes",
     *     "get_favorite", "get_favorites", "get_comment", "get_comments",
     *     "get_repeated_questions", "get_questions_corrections", "get_question_corrections",
     *     "get_votes"
     * })
     */
    private $id;

    /**
     * @ORM\Column(name="custom_id", type="text", length=255, nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $customId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="questions")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $user;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin", inversedBy="questions")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question"
     * })
     */
    private $admin;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $year;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"post_question"})
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes", "get_questions_corrections", "get_question_corrections"
     * })
     * @Annotation\Accessor(setter="setSerializedAccessorType")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="question_number", type="string", nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $questionNumber;

    /**
     * @var string
     * @ORM\Column(name="image_url", type="string", length=255, options={"fixed" = true}, nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "get_note", "get_notes",
     * })
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $notes;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $text;

    /**
     * @var ArrayCollection|Reports[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Reports", mappedBy="questions", cascade={"persist"})
     */
    private $report;

    /**
     * @var ArrayCollection|Notes[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notes", mappedBy="questions", cascade={"persist" , "remove"})
     * @Annotation\Groups({
     *     "get_question", "get_questions"
     * })
     */
    private $note;

    /**
     * @var ArrayCollection|QuestionAnswers[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\QuestionAnswers", mappedBy="questions", cascade={"persist" , "remove"})
     * @Assert\Valid
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\QuestionAnswers>")
     * @Annotation\Accessor(setter="setAccessorQuestionAnswers")
     */
    private $questionAnswers;

    /**
     * @var ArrayCollection|UserQuestionAnswerOpen[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerOpen", mappedBy="questions", cascade={"persist" , "remove"})
     */
    private $questionAnswersOpen;

    /**
     * @var ArrayCollection|Favorites[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Favorites", mappedBy="questions", cascade={"persist", "remove"})
     */
    private $favorites;

    /**
     * @var ArrayCollection|RepeatedQuestions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RepeatedQuestions", mappedBy="questions", cascade={"persist", "remove"})
     */
    private $repeatedQuestions;

    /**
     * @var ArrayCollection|UserQuestionAnswerResult[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerResult", mappedBy="questions", cascade={"persist", "remove"})
     */
    private $userQuestionAnswerResult;

    /**
     * @var ArrayCollection|Comments[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comments", mappedBy="questions", cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @var Semesters
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Semesters", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Type("AppBundle\Entity\Semesters")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $semesters;

    /**
     * @var ExamPeriods
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ExamPeriods", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Type("AppBundle\Entity\ExamPeriods")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $examPeriods;

    /**
     * @var SubCourses
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubCourses", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Type("AppBundle\Entity\SubCourses")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_notes", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $subCourses;

    /**
     * @var Lectors
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lectors", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Type("AppBundle\Entity\Lectors")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $lectors;

    /**
     * @var CoursesOfStudy
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CoursesOfStudy", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Type("AppBundle\Entity\CoursesOfStudy")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $coursesOfStudy;

    /**
     * @var Courses
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Courses", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Type("AppBundle\Entity\Courses")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $courses;

    /**
     * @var ArrayCollection|QuestionCorrections[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\QuestionCorrections", mappedBy="questions", cascade={"persist", "remove"})
     */
    private $questionCorrections;

    /**
     * @var ArrayCollection|Votes[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Votes", mappedBy="questions", cascade={"persist", "remove"})
     */
    private $votes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="votes_at", type="datetime", nullable=true)
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $votesAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->report = new ArrayCollection();
        $this->note = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->questionAnswers = new ArrayCollection();
        $this->questionAnswersOpen = new ArrayCollection();
        $this->repeatedQuestions = new ArrayCollection();
        $this->userQuestionAnswerResult = new ArrayCollection();
        $this->questionCorrections = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setQuestionNumber();
    }

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $this->setQuestionNumber();
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("integer")
     * @Annotation\SerializedName("votes_count")
     * @Annotation\Groups({"get_question", "get_questions"})
     */
    public function getSerializedVotesCount()
    {
        return $this->getVotes()->count();
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"get_question", "get_questions"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_question", "get_questions"})
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
     * Set customId.
     *
     * @param string $customId
     *
     * @return Questions
     */
    public function setCustomId($customId)
    {
        $this->customId = $customId;

        return $this;
    }

    /**
     * Get customId.
     *
     * @return string
     */
    public function getCustomId()
    {
        return $this->customId;
    }

    /**
     * Set year.
     *
     * @param int $year
     *
     * @return Questions
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
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
     * @return Questions
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
     * @param string $questionNumber
     *
     * @return Questions
     */
    public function setQuestionNumber($questionNumber = null)
    {
        if ($questionNumber) {
            $this->questionNumber = $questionNumber;
        } else {
            $this->questionNumber = $this->getCourses()->getId().'/'.$this->getYear().'_'.$this->getExamPeriods()->getId();
        }

        return $this;
    }

    /**
     * Get questionNumber.
     *
     * @return string
     */
    public function getQuestionNumber()
    {
        return $this->questionNumber;
    }

    /**
     * Set imageUrl.
     *
     * @param string $imageUrl
     *
     * @return Questions
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl.
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set notes.
     *
     * @param string $notes
     *
     * @return Questions
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param $text
     *
     * @return Questions
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Questions
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
     * Add report.
     *
     * @param \AppBundle\Entity\Reports $report
     *
     * @return Questions
     */
    public function addReport(\AppBundle\Entity\Reports $report)
    {
        $this->report[] = $report;

        return $this;
    }

    /**
     * Remove report.
     *
     * @param \AppBundle\Entity\Reports $report
     */
    public function removeReport(\AppBundle\Entity\Reports $report)
    {
        $this->report->removeElement($report);
    }

    /**
     * Get report.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Add note.
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return Questions
     */
    public function addNote(\AppBundle\Entity\Notes $note)
    {
        $this->note[] = $note;

        return $this;
    }

    /**
     * @param Notes[] $notes
     *
     * @return $this
     */
    public function setNoteCollection(array $notes)
    {
        $this->getNote()->clear();
        foreach ($notes as $note) {
            $this->addNote($note);
        }

        return $this;
    }

    /**
     * Remove note.
     *
     * @param \AppBundle\Entity\Notes $note
     */
    public function removeNote(\AppBundle\Entity\Notes $note)
    {
        $this->note->removeElement($note);
    }

    /**
     * Get note.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNote()
    {
        if (!$this->note) {
            return $this->note = new ArrayCollection();
        }

        return $this->note;
    }

    /**
     * Add favorite.
     *
     * @param \AppBundle\Entity\Notes $favorite
     *
     * @return Questions
     */
    public function addFavorite(\AppBundle\Entity\Notes $favorite)
    {
        $this->favorites[] = $favorite;

        return $this;
    }

    /**
     * Remove favorite.
     *
     * @param \AppBundle\Entity\Notes $favorite
     */
    public function removeFavorite(\AppBundle\Entity\Notes $favorite)
    {
        $this->favorites->removeElement($favorite);
    }

    /**
     * Get favorites.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavorites()
    {
        return $this->favorites ? $this->favorites : new ArrayCollection();
    }

    /**
     * Add comment.
     *
     * @param \AppBundle\Entity\Comments $comment
     *
     * @return Questions
     */
    public function addComment(\AppBundle\Entity\Comments $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param \AppBundle\Entity\Comments $comment
     */
    public function removeComment(\AppBundle\Entity\Comments $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set semesters.
     *
     * @param \AppBundle\Entity\Semesters $semesters
     *
     * @return Questions
     */
    public function setSemesters(\AppBundle\Entity\Semesters $semesters = null)
    {
        $this->semesters = $semesters;

        return $this;
    }

    /**
     * Get semesters.
     *
     * @return \AppBundle\Entity\Semesters
     */
    public function getSemesters()
    {
        return $this->semesters;
    }

    /**
     * Set examPeriods.
     *
     * @param \AppBundle\Entity\ExamPeriods $examPeriods
     *
     * @return Questions
     */
    public function setExamPeriods(\AppBundle\Entity\ExamPeriods $examPeriods = null)
    {
        $this->examPeriods = $examPeriods;

        return $this;
    }

    /**
     * Get examPeriods.
     *
     * @return \AppBundle\Entity\ExamPeriods
     */
    public function getExamPeriods()
    {
        return $this->examPeriods;
    }

    /**
     * Set subCourses.
     *
     * @param \AppBundle\Entity\SubCourses $subCourses
     *
     * @return Questions
     */
    public function setSubCourses(\AppBundle\Entity\SubCourses $subCourses = null)
    {
        $this->subCourses = $subCourses;

        return $this;
    }

    /**
     * Get subCourses.
     *
     * @return \AppBundle\Entity\SubCourses
     */
    public function getSubCourses()
    {
        return $this->subCourses;
    }

    /**
     * Set lectors.
     *
     * @param \AppBundle\Entity\Lectors $lectors
     *
     * @return Questions
     */
    public function setLectors(\AppBundle\Entity\Lectors $lectors = null)
    {
        $this->lectors = $lectors;

        return $this;
    }

    /**
     * Get lectors.
     *
     * @return \AppBundle\Entity\Lectors
     */
    public function getLectors()
    {
        return $this->lectors;
    }

    /**
     * Set admin.
     *
     * @param \AppBundle\Entity\Admin $admin
     *
     * @return Questions
     */
    public function setAdmin(\AppBundle\Entity\Admin $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin.
     *
     * @return \AppBundle\Entity\Admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Add questionAnswer.
     *
     * @param \AppBundle\Entity\QuestionAnswers $questionAnswer
     *
     * @return Questions
     */
    public function addQuestionAnswer(\AppBundle\Entity\QuestionAnswers $questionAnswer)
    {
        $this->questionAnswers[] = $questionAnswer;
        $questionAnswer->setQuestions($this);

        return $this;
    }

    public function setAccessorQuestionAnswers($questionAnswers)
    {
        $this->getQuestionAnswers()->clear();
        foreach ($questionAnswers as $questionAnswer) {
            $this->addQuestionAnswer($questionAnswer);
        }
    }

    /**
     * Remove questionAnswer.
     *
     * @param \AppBundle\Entity\QuestionAnswers $questionAnswer
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeQuestionAnswer(\AppBundle\Entity\QuestionAnswers $questionAnswer)
    {
        return $this->questionAnswers->removeElement($questionAnswer);
    }

    /**
     * Get questionAnswers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionAnswers()
    {
        if (!$this->questionAnswers) {
            $this->questionAnswers = new ArrayCollection();
        }

        return $this->questionAnswers;
    }

    /**
     * Add questionAnswersOpen.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerOpen $questionAnswersOpen
     *
     * @return Questions
     */
    public function addQuestionAnswersOpen(\AppBundle\Entity\UserQuestionAnswerOpen $questionAnswersOpen)
    {
        $this->questionAnswersOpen[] = $questionAnswersOpen;

        return $this;
    }

    /**
     * Remove questionAnswersOpen.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerOpen $questionAnswersOpen
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeQuestionAnswersOpen(\AppBundle\Entity\UserQuestionAnswerOpen $questionAnswersOpen)
    {
        return $this->questionAnswersOpen->removeElement($questionAnswersOpen);
    }

    /**
     * Get questionAnswersOpen.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionAnswersOpen()
    {
        return $this->questionAnswersOpen;
    }

    /**
     * Add repeatedQuestion.
     *
     * @param \AppBundle\Entity\RepeatedQuestions $repeatedQuestion
     *
     * @return Questions
     */
    public function addRepeatedQuestion(\AppBundle\Entity\RepeatedQuestions $repeatedQuestion)
    {
        $this->repeatedQuestions[] = $repeatedQuestion;

        return $this;
    }

    /**
     * Remove repeatedQuestion.
     *
     * @param \AppBundle\Entity\RepeatedQuestions $repeatedQuestion
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeRepeatedQuestion(\AppBundle\Entity\RepeatedQuestions $repeatedQuestion)
    {
        return $this->repeatedQuestions->removeElement($repeatedQuestion);
    }

    /**
     * Get repeatedQuestions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRepeatedQuestions()
    {
        return $this->repeatedQuestions ? $this->repeatedQuestions : new ArrayCollection();
    }

    /**
     * Add userQuestionAnswerResult.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult
     *
     * @return Questions
     */
    public function addUserQuestionAnswerResult(\AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult)
    {
        $this->userQuestionAnswerResult[] = $userQuestionAnswerResult;

        return $this;
    }

    /**
     * Remove userQuestionAnswerResult.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeUserQuestionAnswerResult(\AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult)
    {
        return $this->userQuestionAnswerResult->removeElement($userQuestionAnswerResult);
    }

    /**
     * Get userQuestionAnswerResult.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserQuestionAnswerResult()
    {
        return $this->userQuestionAnswerResult;
    }

    /**
     * Set coursesOfStudy.
     *
     * @param null|\AppBundle\Entity\CoursesOfStudy $coursesOfStudy
     *
     * @return Questions
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
     * @return Questions
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

    /**
     * Add questionCorrection.
     *
     * @param \AppBundle\Entity\QuestionCorrections $questionCorrection
     *
     * @return Questions
     */
    public function addQuestionCorrection(\AppBundle\Entity\QuestionCorrections $questionCorrection)
    {
        $this->questionCorrections[] = $questionCorrection;

        return $this;
    }

    /**
     * Remove questionCorrection.
     *
     * @param \AppBundle\Entity\QuestionCorrections $questionCorrection
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeQuestionCorrection(\AppBundle\Entity\QuestionCorrections $questionCorrection)
    {
        return $this->questionCorrections->removeElement($questionCorrection);
    }

    /**
     * Get questionCorrections.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionCorrections()
    {
        return $this->questionCorrections;
    }

    /**
     * Add vote.
     *
     * @param \AppBundle\Entity\Votes $vote
     *
     * @return Questions
     */
    public function addVote(\AppBundle\Entity\Votes $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote.
     *
     * @param \AppBundle\Entity\Votes $vote
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeVote(\AppBundle\Entity\Votes $vote)
    {
        return $this->votes->removeElement($vote);
    }

    /**
     * Get votes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes ? $this->votes : new ArrayCollection();
    }

    /**
     * Set votesAt.
     *
     * @param null|\DateTime $votesAt
     *
     * @return Questions
     */
    public function setVotesAt($votesAt = null)
    {
        $this->votesAt = $votesAt;

        return $this;
    }

    /**
     * Get votesAt.
     *
     * @return null|\DateTime
     */
    public function getVotesAt()
    {
        return $this->votesAt;
    }
}
