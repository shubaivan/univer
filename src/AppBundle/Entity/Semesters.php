<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="semesters")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SemestersRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @AssertBridge\UniqueEntity(
 *     groups={"post_semester", "put_semester"},
 *     fields="name",
 *     errorPath="not valid",
 *     message="This name is already in use."
 * )
 */
class Semesters
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_semester", "get_semesters", "get_question", "get_questions",
     *     "get_events", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"fixed" = true}, nullable=false)
     * @Annotation\Groups({
     *     "get_semester", "get_semesters", "post_semester", "put_semester",
     *     "get_questions", "get_question", "get_questions_corrections",
     *     "get_question_corrections", "get_events"
     * })
     * @Assert\NotBlank(groups={"post_semester", "put_semester"})
     */
    private $name;

    /**
     * @var ArrayCollection|Questions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Questions", mappedBy="semesters", cascade={"persist"})
     */
    private $questions;

    /**
     * @var ArrayCollection|Events[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Events", mappedBy="semesters", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var ArrayCollection|QuestionCorrections[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\QuestionCorrections", mappedBy="semesters", cascade={"persist", "remove"})
     */
    private $questionCorrections;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->questionCorrections = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Semesters
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add question.
     *
     * @param \AppBundle\Entity\Questions $question
     *
     * @return Semesters
     */
    public function addQuestion(\AppBundle\Entity\Questions $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question.
     *
     * @param \AppBundle\Entity\Questions $question
     */
    public function removeQuestion(\AppBundle\Entity\Questions $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"get_semester", "get_semesters"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_semester", "get_semesters"})
     */
    public function getSerializedUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add event.
     *
     * @param \AppBundle\Entity\Events $event
     *
     * @return Semesters
     */
    public function addEvent(\AppBundle\Entity\Events $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event.
     *
     * @param \AppBundle\Entity\Events $event
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeEvent(\AppBundle\Entity\Events $event)
    {
        return $this->events->removeElement($event);
    }

    /**
     * Get events.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add questionCorrection.
     *
     * @param \AppBundle\Entity\QuestionCorrections $questionCorrection
     *
     * @return Semesters
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
}
