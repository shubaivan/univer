<?php

namespace AppBundle\Enum;

abstract class AppealTypeEnum
{
    const TYPE_APPEAL = 'appeal';
    const TYPE_ANSWER = 'answer';

    /** @var array user friendly named type */
    protected static $typeName = [
        self::TYPE_APPEAL => 'appeal',
        self::TYPE_ANSWER => 'answer',
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
        return [
            self::TYPE_APPEAL,
            self::TYPE_ANSWER,
        ];
    }
}
