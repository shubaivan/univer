<?php

namespace AppBundle\Entity\Enum;

abstract class QuestionsTypeEnum
{
    const TYPE_OPEN = 'open';
    const TYPE_TEST = 'test';

    /** @var array user friendly named type */
    protected static $typeName = [
        self::TYPE_OPEN => '1',
        self::TYPE_TEST => '2',
    ];

    /**
     * @param string $typeShortName
     *
     * @return string
     */
    public static function getTypeName($typeShortName)
    {
        if (!isset(static::$typeName[$typeShortName])) {
            return "Unknown type ($typeShortName)";
        }

        return static::$typeName[$typeShortName];
    }

    /**
     * @return array<string>
     */
    public static function getAvailableTypes()
    {
        return self::$typeName;
    }
}
