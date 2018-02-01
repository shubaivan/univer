<?php

namespace AppBundle\Entity\Collections\Notes;

use AppBundle\Entity\Collections\AbstractCollectionsInterface;
use JMS\Serializer\Annotation;

class SubCoursesCollection implements AbstractCollectionsInterface
{
    /**
     * @var array|NotesCollection[]
     * @Annotation\Groups({"get_sub_courses"})
     */
    private $collection = [];

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
            $this->collection[$value['sub_courses_name']] = new NotesCollection($value);
        }
    }

    /**
     * @return array|NotesCollection[]
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function getTotal()
    {
        return $this->total;
    }
}
