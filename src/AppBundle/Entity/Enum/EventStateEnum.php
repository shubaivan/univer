<?php

namespace AppBundle\Entity\Enum;

abstract class EventStateEnum
{
    const UNRESOLVED = 'unresolved';
    const NOT_SUCCESSED = 'not_successed';

    /** @var array user friendly named type */
    protected static $typeName = [
        self::UNRESOLVED => self::UNRESOLVED,
        self::NOT_SUCCESSED => self::NOT_SUCCESSED,
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
            self::UNRESOLVED,
            self::NOT_SUCCESSED,
        ];
    }
}
