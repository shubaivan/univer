<?php

namespace AppBundle\Entity\Enum;

abstract class ImprovementSuggestionStatusEnum
{
    const NOT_VIEWED = 'not_viewed';
    const VIEWED = 'viewed';

    /** @var array user friendly named type */
    protected static $typeName = [
        self::NOT_VIEWED => self::NOT_VIEWED,
        self::VIEWED => self::VIEWED,
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
            self::VIEWED,
            self::NOT_VIEWED,
        ];
    }
}
