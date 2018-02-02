<?php

namespace AppBundle\Entity\Collections\Questions;

use AppBundle\DTO\Notes\NotesDTO;
use JMS\Serializer\Annotation;

class QuestionsCollection
{
    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $questionId;

    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $customId;

    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $year;

    /**
     * @var string
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("string")
     */
    private $type;

    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    private $questionNumber;

    /**
     * @var string
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("string")
     */
    private $imageUrl;

    /**
     * @var string
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("string")
     */
    private $notesText;

    /**
     * @var string
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("string")
     */
    private $text;

    /**
     * @var array|NotesDTO[]
     * @Annotation\Groups({"get_sub_courses"})
     */
    private $notes = [];

    /**
     * EbooksCollection constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->questionId = $values['id'];
        $this->customId = $values['customId'];
        $this->year = $values['year'];
        $this->type = $values['type'];
        $this->questionNumber = $values['questionNumber'];
        $this->imageUrl = $values['imageUrl'];
        $this->notesText = $values['notes_text'];
        $this->text = $values['text'];

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
