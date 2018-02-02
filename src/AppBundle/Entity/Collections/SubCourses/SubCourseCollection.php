<?php

namespace AppBundle\Entity\Collections\SubCourses;

use AppBundle\Entity\Collections\AbstractCollectionsInterface;
use AppBundle\Entity\Collections\Questions\QuestionsCollection;
use JMS\Serializer\Annotation;

class SubCourseCollection implements AbstractCollectionsInterface
{
    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $subCoursesId;

    /**
     * @var string
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("string")
     */
    private $subCoursesName;

    /**
     * @var array|QuestionsCollection[]
     * @Annotation\Groups({"get_sub_courses"})
     */
    private $questions = [];

    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $totalQuestions;

    /**
     * EbooksCollection constructor.
     *
     * @param array $values
     * @param null  $total
     */
    public function __construct(array $values, $total = null)
    {
        $this->totalQuestions = count($values['questions']);
        $this->subCoursesName = $values['sub_courses_name'];
        $this->subCoursesId = $values['sub_courses_id'];
        foreach ($values['questions'] as $value) {
            $this->questions[] = new QuestionsCollection($value);
        }
    }

    /**
     * @return array|QuestionsCollection[]
     */
    public function getCollection()
    {
        return $this->questions;
    }

    /**
     * @return array
     */
    public function getTotal()
    {
        return $this->total;
    }
}
