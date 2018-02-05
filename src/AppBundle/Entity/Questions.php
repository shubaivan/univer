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
 * @ORM\Table(name="questions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\QuestionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Questions
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "get_note", "get_notes"
     * })
     */
    private $id;

    /**
     * @ORM\Column(name="custom_id", type="text", length=255, nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes"
     * })
     */
    private $customId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="questions")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question"
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
     *     "get_note", "get_notes"
     * })
     */
    private $year;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"post_course", "put_course"})
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes"
     * })
     * @Annotation\Accessor(setter="setSerializedAccessorType")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="question_number", type="integer", nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes"
     * })
     */
    private $questionNumber;

    /**
     * @var string
     * @ORM\Column(name="image_url", type="string", length=255, options={"fixed" = true}, nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "get_note", "get_notes"
     * })
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question",
     *     "get_note", "get_notes"
     * })
     */
    private $notes;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question"
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notes", mappedBy="questions", cascade={"persist"})
     * @Annotation\Groups({
     *     "get_question", "get_questions"
     * })
     */
    private $note;

    /**
     * @var ArrayCollection|Favorites[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Favorites", mappedBy="questions", cascade={"persist"})
     */
    private $favorites;

    /**
     * @var ArrayCollection|Comments[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comments", mappedBy="questions", cascade={"persist"})
     */
    private $comments;

    /**
     * @var Semesters
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Semesters", inversedBy="questions")
     * @Assert\NotBlank(groups={"post_question", "put_question"})
     * @Annotation\Type("AppBundle\Entity\Semesters")
     * @Annotation\Groups({
     *     "get_question", "get_questions", "post_question", "put_question"
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
     *     "get_question", "get_questions", "post_question", "put_question"
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
     *     "get_notes"
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
     *     "get_question", "get_questions", "post_question", "put_question"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $lectors;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->report = new \Doctrine\Common\Collections\ArrayCollection();
        $this->note = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favorites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param int $questionNumber
     *
     * @return Questions
     */
    public function setQuestionNumber($questionNumber)
    {
        $this->questionNumber = $questionNumber;

        return $this;
    }

    /**
     * Get questionNumber.
     *
     * @return int
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
        return $this->favorites;
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
}
