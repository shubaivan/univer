<?php

namespace AppBundle\Entity\Collections\Notes;

use AppBundle\DTO\Notes\NotesDTO;
use JMS\Serializer\Annotation;

class NotesCollection implements NotesCollectionInterface
{
    /**
     * @var array|NotesDTO[]
     * @Annotation\Groups({"get_sub_courses"})
     */
    private $notes = [];

    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $subCoutsesId;

    /**
     * @var string
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("string")
     */
    private $subCoursesName;

    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $questionId;

    /**
     * EbooksCollection constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->subCoutsesId = $values['sub_courses_id'];
        $this->subCoursesName = $values['sub_courses_name'];
        $this->questionId = $values['question_id'];
        foreach ($values['notes'] as $value) {
            $this->notes[] = new NotesDTO($value);
        }
    }

    /**
     * @return array|NotesDTO[]
     */
    public function getCollection()
    {
        return $this->notes;
    }
}
