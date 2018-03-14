<?php

namespace AppBundle\Entity\Enum;

abstract class ProviderTypeEnum
{
    const TYPE_PROVIDER_COMMENT = 'comment';

    /** @var array user friendly named type */
    protected static $typeName = [
        self::TYPE_PROVIDER_COMMENT => 'comment',
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
            self::TYPE_PROVIDER_COMMENT,
        ];
    }
}
