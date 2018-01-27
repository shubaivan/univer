<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;

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
     *     "get_course_of_study", "get_courses_of_study", "get_course", "get_courses"
     * })
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Annotation\Groups({
     *     "get_course_of_study", "get_courses_of_study", "post_course_of_study", "put_course_of_study",
     *     "get_course", "get_courses"
     * })
     */
    private $name;

    /**
     * @var ArrayCollection|Courses[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Courses", mappedBy="coursesOfStudy", cascade={"persist"})
     */
    private $courses;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add course.
     *
     * @param \AppBundle\Entity\Courses $course
     *
     * @return CoursesOfStudy
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
        return $this->courses;
    }
}
