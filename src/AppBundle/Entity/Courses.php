<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="courses")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CoursesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @AssertBridge\UniqueEntity(
 *     groups={"post_course", "put_course"},
 *     fields="name",
 *     errorPath="not valid",
 *     message="This name is already in use."
 * )
 */
class Courses
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_course", "get_courses", "get_sub_course", "get_sub_courses",
     *     "get_course_of_study", "get_courses_of_study"
     * })
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Annotation\Groups({
     *     "post_course", "put_course", "get_course", "get_courses",
     *     "get_sub_course", "get_sub_courses", "get_course_of_study", "get_courses_of_study"
     * })
     * @Assert\NotBlank(groups={"post_course", "put_course"})
     * @Assert\Length(
     *     groups={"post_course", "put_course"},
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     */
    private $name;

    /**
     * @var CoursesOfStudy
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CoursesOfStudy", inversedBy="courses")
     * @Annotation\Groups({
     *     "post_course", "put_course", "get_course", "get_courses"
     * })
     * @Annotation\Type("AppBundle\Entity\CoursesOfStudy")
     * @Annotation\SerializedName("courses_of_study")
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $coursesOfStudy;

    /**
     * @var ArrayCollection|SubCourses[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubCourses", mappedBy="courses", cascade={"persist"})
     * @Annotation\Groups({
     *     "get_course", "get_courses"
     * })
     */
    private $subCourses;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->subCourses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Courses
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
     * Set coursesOfStudy.
     *
     * @param \AppBundle\Entity\CoursesOfStudy $coursesOfStudy
     *
     * @return Courses
     */
    public function setCoursesOfStudy(\AppBundle\Entity\CoursesOfStudy $coursesOfStudy = null)
    {
        $this->coursesOfStudy = $coursesOfStudy;

        return $this;
    }

    /**
     * Get coursesOfStudy.
     *
     * @return \AppBundle\Entity\CoursesOfStudy
     */
    public function getCoursesOfStudy()
    {
        return $this->coursesOfStudy;
    }

    /**
     * Add subCourse.
     *
     * @param \AppBundle\Entity\SubCourses $subCourse
     *
     * @return Courses
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
     */
    public function removeSubCourse(\AppBundle\Entity\SubCourses $subCourse)
    {
        $this->subCourses->removeElement($subCourse);
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
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"get_course", "get_courses"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_course", "get_courses"})
     */
    public function getSerializedUpdatedAt()
    {
        return $this->updatedAt;
    }
}
