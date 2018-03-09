<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Enum\EventStateEnum;
use AppBundle\Validator\Constraints\ConditionAuthor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="events")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\EventsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ConditionAuthor(groups={"post_event"})
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
    private $type = 'question';

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Courses", inversedBy="events", cascade={"persist"})
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\Courses>")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $courses;

    /**
     * @var ArrayCollection|SubCourses[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SubCourses", inversedBy="events", cascade={"persist"})
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\SubCourses>")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $subCourses;

    /**
     * @var ArrayCollection|Lectors[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Lectors", inversedBy="events", cascade={"persist"})
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\Lectors>")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $lectors;

    /**
     * @var ArrayCollection|ExamPeriods[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ExamPeriods", inversedBy="events", cascade={"persist"})
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\ExamPeriods>")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $examPeriods;

    /**
     * @var ArrayCollection|Semesters[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Semesters", inversedBy="events", cascade={"persist"})
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
     * @ORM\Column(type="integer", nullable=false)
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $count = 10;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $page = 1;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $sortBy = 'createdAt';

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $sortOrder = 'DESC';

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Annotation\Type("array<integer>")
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $years = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Annotation\Groups({
     *     "post_event"
     * })
     */
    private $search;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Annotation\Groups({
     *     "post_event"
     * })
     * @Annotation\Accessor(setter="setSerializedAccessorUserState")
     */
    private $userState;

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
     * @return ArrayCollection|Courses[]
     */
    public function getCourses()
    {
        return $this->courses ? $this->courses : new ArrayCollection();
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
        return $this->subCourses ? $this->subCourses : new ArrayCollection();
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
        return $this->lectors ? $this->lectors : new ArrayCollection();
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
        return $this->examPeriods ? $this->examPeriods : new ArrayCollection();
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
        return $this->semesters ? $this->semesters : new ArrayCollection();
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

    /**
     * @return ParameterBag
     */
    public function checkCondition()
    {
        $parameters = new ParameterBag();

        if ($this->getUserState()) {
            $parameters->set('user_state', $this->getUserState());
        }

        if ($this->getUser()) {
            $parameters->set('user', $this->getUser()->getId());
        }

        if ($this->getSearch()) {
            $parameters->set('search', $this->getSearch());
        }

        if ($this->getYears()) {
            $parameters->set('years', $this->getYears());
        }

        if ($this->getCoursesOfStudy()) {
            $parameters->set('courses_of_study', $this->getCoursesOfStudy()->getId());
        }

        if ($this->getCourses()->count()) {
            $coursData = [];
            foreach ($this->getCourses() as $cours) {
                $coursData[] = $cours->getId();
            }
            $parameters->set('courses', $coursData);
        }

        if ($this->getSubCourses()->count()) {
            $subCoursData = [];
            foreach ($this->getSubCourses() as $subCours) {
                $subCoursData[] = $subCours->getId();
            }
            $parameters->set('sub_courses', $subCoursData);
        }

        if ($this->getLectors()->count()) {
            $lectorData = [];
            foreach ($this->getLectors() as $lector) {
                $lectorData[] = $lector->getId();
            }
            $parameters->set('lectors', $lectorData);
        }

        if ($this->getExamPeriods()->count()) {
            $examPeriodData = [];
            foreach ($this->getExamPeriods() as $examPeriod) {
                $examPeriodData[] = $examPeriod->getId();
            }
            $parameters->set('exam_periods', $examPeriodData);
        }

        if ($this->getSemesters()->count()) {
            $semesterData = [];
            foreach ($this->getSemesters() as $semester) {
                $semesterData[] = $semester->getId();
            }
            $parameters->set('semesters', $semesterData);
        }

        $parameters->set('sort_by', $this->getSortBy());
        $parameters->set('sort_order', $this->getSortOrder());
        $parameters->set('count', $this->getCount());
        $parameters->set('page', $this->getPage());

        return $parameters;
    }

    /**
     * Set count.
     *
     * @param int $count
     *
     * @return Events
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set page.
     *
     * @param int $page
     *
     * @return Events
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set sortBy.
     *
     * @param string $sortBy
     *
     * @return Events
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * Get sortBy.
     *
     * @return string
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * Set sortOrder.
     *
     * @param string $sortOrder
     *
     * @return Events
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set search.
     *
     * @param string $search
     *
     * @return Events
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search.
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $userState
     * @return bool
     */
    public function setSerializedAccessorUserState($userState)
    {
        if ($userState === "") {
            return false;
        }
        $this->setUserState($userState);
    }

    /**
     * Set userState.
     *
     * @param null|string $userState
     *
     * @return Events
     */
    public function setUserState($userState = null)
    {
        if (!in_array($userState, EventStateEnum::getAvailableTypes(), true)) {
            throw new \InvalidArgumentException(
                'Invalid type. Available type: '.implode(',', EventStateEnum::getAvailableTypes())
            );
        }

        $this->userState = $userState;

        return $this;
    }

    /**
     * Get userState.
     *
     * @return null|string
     */
    public function getUserState()
    {
        return $this->userState;
    }

    /**
     * Set years.
     *
     * @param array|null $years
     *
     * @return Events
     */
    public function setYears($years = null)
    {
        $this->years = $years;

        return $this;
    }

    /**
     * Get years.
     *
     * @return array|null
     */
    public function getYears()
    {
        return $this->years;
    }
}
