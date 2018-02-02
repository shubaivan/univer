<?php

namespace AppBundle\Entity\Collections\SubCourses;

use AppBundle\Entity\Collections\AbstractCollectionsInterface;
use JMS\Serializer\Annotation;

class SubCoursesCollection implements AbstractCollectionsInterface
{
    /**
     * @var array|SubCourseCollection[]
     * @Annotation\Groups({"get_sub_courses"})
     */
    private $subCourses = [];

    /**
     * @var []
     * @Annotation\Groups({"get_sub_courses"})
     */
    private $total;

    /**
     * EbooksCollection constructor.
     *
     * @param array $values
     * @param null  $total
     */
    public function __construct(array $values, $total = null)
    {
        $this->total = $total;
        foreach ($values as $value) {
            $this->subCourses[$value['sub_courses_name']] = new SubCourseCollection($value);
        }
    }

    /**
     * @return array|SubCourseCollection[]
     */
    public function getCollection()
    {
        return $this->subCourses;
    }

    /**
     * @return array
     */
    public function getTotal()
    {
        return $this->total;
    }
}
