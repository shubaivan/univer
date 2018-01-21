<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="courses")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CoursesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Courses
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var CoursesOfStudy
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CoursesOfStudy", inversedBy="courses")
     */
    private $coursesOfStudy;

    /**
     * @var ArrayCollection|SubCourses[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubCourses", mappedBy="courses", cascade={"persist"})
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
}
