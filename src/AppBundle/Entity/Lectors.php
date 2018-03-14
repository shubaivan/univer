<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="lectors",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="fio_unique",
 *            columns={"first_name", "last_name"})
 *    }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\LectorsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @UniqueEntity(
 *     groups={"post_lector", "put_lector"},
 *     fields={"firstName", "lastName"}
 * )
 */
class Lectors
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_lector", "get_lectors", "get_question", "get_questions",
     *     "get_events", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", length=100, nullable=false)
     * @Annotation\Groups({
     *     "get_lector", "get_lectors", "post_lector", "put_lector",
     *     "get_questions", "get_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Assert\Length(
     *     groups={"post_lector", "put_lector"},
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=100, nullable=false)
     * @Annotation\Groups({
     *     "get_lector", "get_lectors", "post_lector", "put_lector",
     *     "get_questions", "get_question", "get_questions_corrections", "get_question_corrections"
     * })
     * @Assert\Length(
     *     groups={"post_lector", "put_lector"},
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your last name must be at least {{ limit }} characters long",
     *      maxMessage = "Your last name cannot be longer than {{ limit }} characters"
     * )
     */
    private $lastName;

    /**
     * @var ArrayCollection|Questions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Questions", mappedBy="lectors", cascade={"persist"})
     */
    private $questions;

    /**
     * @var ArrayCollection|Events[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Events", mappedBy="lectors", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var ArrayCollection|QuestionCorrections[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\QuestionCorrections", mappedBy="lectors", cascade={"persist", "remove"})
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
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Lectors
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return Lectors
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Add question.
     *
     * @param \AppBundle\Entity\Questions $question
     *
     * @return Lectors
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
     * @Annotation\Groups({"get_lector", "get_lectors"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_lector", "get_lectors"})
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
     * @return Lectors
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
     * @return Lectors
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
