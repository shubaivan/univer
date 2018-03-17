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
 * @ORM\Table(name="sub_cources")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubCoursesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @AssertBridge\UniqueEntity(
 *     groups={"post_sub_course", "put_sub_course"},
 *     fields="name",
 *     errorPath="not valid",
 *     message="This name is already in use."
 * )
 */
class SubCourses
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_sub_course", "get_sub_courses", "get_question", "get_questions",
     *     "get_course", "get_courses", "get_course_of_study", "get_courses_of_study",
     *     "get_notes", "get_events", "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Annotation\Groups({
     *     "get_sub_course", "get_sub_courses", "post_sub_course", "put_sub_course",
     *     "get_course", "get_courses", "get_course_of_study", "get_courses_of_study",
     *     "get_questions", "get_question", "get_questions_corrections",
     *     "get_question_corrections", "get_events"
     * })
     */
    private $name;

    /**
     * @var ArrayCollection|Courses[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Courses", inversedBy="subCourses", cascade={"persist", "remove"})
     * @Annotation\Groups({
     *     "get_sub_course", "get_sub_courses", "post_sub_course", "put_sub_course",
     *     "get_questions", "get_question",
     *     "get_questions_corrections", "get_question_corrections"
     * })
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\Courses>")
     * @Assert\NotBlank(groups={"post_sub_course", "put_sub_course"})
     */
    private $courses;

    /**
     * @var ArrayCollection|Questions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Questions", mappedBy="subCourses", cascade={"persist"})
     */
    private $questions;

    /**
     * @var ArrayCollection|Events[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Events", mappedBy="subCourses", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var ArrayCollection|QuestionCorrections[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\QuestionCorrections", mappedBy="subCourses", cascade={"persist", "remove"})
     */
    private $questionCorrections;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->courses = new ArrayCollection();
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
     * @return SubCourses
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
     * @return SubCourses
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
     * @Annotation\Groups({"get_sub_course", "get_sub_courses"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_sub_course", "get_sub_courses"})
     */
    public function getSerializedUpdatedAt()
    {
        return $this->updatedAt;
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
        $course->addSubCourse($this);

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
     * Add event.
     *
     * @param \AppBundle\Entity\Events $event
     *
     * @return SubCourses
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
     * @return SubCourses
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
