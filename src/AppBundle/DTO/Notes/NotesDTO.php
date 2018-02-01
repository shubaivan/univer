<?php

namespace AppBundle\DTO\Notes;

use AppBundle\DTO\AbstractTotalDTO;
use JMS\Serializer\Annotation;

class NotesDTO extends AbstractTotalDTO
{
    const ID = 'id';
    const TEXT = 'text';

    /**
     * @var array
     */
    protected static $fieldsMapping = [
        self::ID => 'id',
        self::TEXT => 'text',
    ];

    /**
     * @var int
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("integer")
     */
    protected $id;

    /**
     * @var string
     * @Annotation\Groups({"get_sub_courses"})
     * @Annotation\Type("string")
     */
    protected $text;

    /**
     * Get list of fields keys.
     *
     * @return array
     */
    public static function getFields()
    {
        return array_keys(self::$fieldsMapping);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldsList()
    {
        return self::$fieldsMapping;
    }
}
