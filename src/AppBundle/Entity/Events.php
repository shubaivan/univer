<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="events")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Events
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
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $type;

    /**
     * @var CoursesOfStudy
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CoursesOfStudy", inversedBy="events")
     * @Annotation\Type("AppBundle\Entity\CoursesOfStudy")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $coursesOfStudy;

    /**
     * @var ArrayCollection|Courses[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Courses", mappedBy="events", cascade={"persist"})
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $courses;

    /**
     * @var ArrayCollection|SubCourses[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubCourses", mappedBy="events", cascade={"persist"})
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $subCourses;

    /**
     * @var ArrayCollection|Lectors[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Lectors", mappedBy="events", cascade={"persist"})
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $lectors;

    /**
     * @var ArrayCollection|ExamPeriods[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ExamPeriods", mappedBy="events", cascade={"persist"})
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $examPeriods;

    /**
     * @var ArrayCollection|Semesters[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Semesters", mappedBy="events", cascade={"persist"})
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\Semesters>")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $semesters;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="events")
     * @Evence\onSoftDelete(type="SET NULL")
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $user;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin", inversedBy="events")
     * @Annotation\Type("AppBundle\Entity\Admin")
     * @Evence\onSoftDelete(type="SET NULL")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $admin;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subCourses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lectors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->examPeriods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->semesters = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set text.
     *
     * @param null|string $text
     *
     * @return Events
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
     * Set type.
     *
     * @param string $type
     *
     * @return Events
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * Set coursesOfStudy.
     *
     * @param null|\AppBundle\Entity\CoursesOfStudy $coursesOfStudy
     *
     * @return Events
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
     * Add course.
     *
     * @param \AppBundle\Entity\Courses $course
     *
     * @return Events
     */
    public function addCourse(\AppBundle\Entity\Courses $course)
    {
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Remove course.
     *
     * @param \AppBundle\Entity\Courses $course
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeCourse(\AppBundle\Entity\Courses $course)
    {
        return $this->courses->removeElement($course);
    }

    /**
     * Get courses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Add subCourse.
     *
     * @param \AppBundle\Entity\SubCourses $subCourse
     *
     * @return Events
     */
    public function addSubCourse(\AppBundle\Entity\SubCourses $subCourse)
    {
        $this->subCourses[] = $subCourse;

        return $this;
    }

    /**
     * Remove subCourse.
     *
     * @param \AppBundle\Entity\SubCourses $subCourse
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeSubCourse(\AppBundle\Entity\SubCourses $subCourse)
    {
        return $this->subCourses->removeElement($subCourse);
    }

    /**
     * Get subCourses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubCourses()
    {
        return $this->subCourses;
    }

    /**
     * Add lector.
     *
     * @param \AppBundle\Entity\Lectors $lector
     *
     * @return Events
     */
    public function addLector(\AppBundle\Entity\Lectors $lector)
    {
        $this->lectors[] = $lector;

        return $this;
    }

    /**
     * Remove lector.
     *
     * @param \AppBundle\Entity\Lectors $lector
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeLector(\AppBundle\Entity\Lectors $lector)
    {
        return $this->lectors->removeElement($lector);
    }

    /**
     * Get lectors.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLectors()
    {
        return $this->lectors;
    }

    /**
     * Add examPeriod.
     *
     * @param \AppBundle\Entity\ExamPeriods $examPeriod
     *
     * @return Events
     */
    public function addExamPeriod(\AppBundle\Entity\ExamPeriods $examPeriod)
    {
        $this->examPeriods[] = $examPeriod;

        return $this;
    }

    /**
     * Remove examPeriod.
     *
     * @param \AppBundle\Entity\ExamPeriods $examPeriod
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeExamPeriod(\AppBundle\Entity\ExamPeriods $examPeriod)
    {
        return $this->examPeriods->removeElement($examPeriod);
    }

    /**
     * Get examPeriods.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExamPeriods()
    {
        return $this->examPeriods;
    }

    /**
     * Add semester.
     *
     * @param \AppBundle\Entity\Semesters $semester
     *
     * @return Events
     */
    public function addSemester(\AppBundle\Entity\Semesters $semester)
    {
        $this->semesters[] = $semester;

        return $this;
    }

    /**
     * Remove semester.
     *
     * @param \AppBundle\Entity\Semesters $semester
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeSemester(\AppBundle\Entity\Semesters $semester)
    {
        return $this->semesters->removeElement($semester);
    }

    /**
     * Get semesters.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSemesters()
    {
        return $this->semesters;
    }

    /**
     * Set user.
     *
     * @param null|\AppBundle\Entity\User $user
     *
     * @return Events
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
     * Set admin.
     *
     * @param null|\AppBundle\Entity\Admin $admin
     *
     * @return Events
     */
    public function setAdmin(\AppBundle\Entity\Admin $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin.
     *
     * @return null|\AppBundle\Entity\Admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    public function checkCondition()
    {
    }
}
