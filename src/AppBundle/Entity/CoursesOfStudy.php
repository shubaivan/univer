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
 * @ORM\Table(name="courses_of_study")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CoursesOfStudyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @AssertBridge\UniqueEntity(
 *     groups={"post_course_of_study", "put_course_of_study"},
 *     fields="name",
 *     errorPath="not valid",
 *     message="This name is already in use."
 * )
 */
class CoursesOfStudy
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_course_of_study", "get_courses_of_study", "get_course", "get_courses",
     *     "get_questions", "get_question", "get_events", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, nullable=false)
     * @Annotation\Groups({
     *     "get_course_of_study", "get_courses_of_study", "post_course_of_study", "put_course_of_study",
     *     "get_course", "get_courses", "get_questions", "get_question", "get_questions_corrections",
     *     "get_question_corrections", "get_events"
     * })
     * @Assert\NotBlank(groups={"post_course_of_study", "put_course_of_study"})
     * @Assert\Length(
     *     groups={"post_course_of_study", "put_course_of_study"},
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     */
    private $name;

    /**
     * @var ArrayCollection|Courses[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Courses", mappedBy="coursesOfStudy", cascade={"persist"})
     * @Annotation\Groups({
     *     "get_course_of_study", "get_courses_of_study"
     * })
     */
    private $courses;

    /**
     * @var ArrayCollection|Events[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Events", mappedBy="coursesOfStudy", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var ArrayCollection|Questions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Questions", mappedBy="coursesOfStudy", cascade={"persist"})
     */
    private $questions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->questions = new ArrayCollection();
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
     * @return CoursesOfStudy
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
     * @param Courses $course
     *
     * @return $this|bool
     */
    public function addCourse(\AppBundle\Entity\Courses $course)
    {
        if ($this->getCourses()->contains($course)) {
            return false;
        }
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Remove course.
     *
     * @param \AppBundle\Entity\Courses $course
     */
    public function removeCourse(\AppBundle\Entity\Courses $course)
    {
        $this->courses->removeElement($course);
    }

    /**
     * Get courses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        if (!$this->courses) {
            $this->courses = new ArrayCollection();
        }

        return $this->courses;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"get_course_of_study", "get_courses_of_study"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_course_of_study", "get_courses_of_study"})
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
     * @return CoursesOfStudy
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
     * Add question.
     *
     * @param \AppBundle\Entity\Questions $question
     *
     * @return CoursesOfStudy
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
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeQuestion(\AppBundle\Entity\Questions $question)
    {
        return $this->questions->removeElement($question);
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
}
