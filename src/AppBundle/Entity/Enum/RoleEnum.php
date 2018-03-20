<?php

namespace AppBundle\Entity\Enum;

abstract class RoleEnum
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_COURSE_COMMITEE = 'COURSE_COMMITEE';

    /** @var array user friendly named type */
    protected static $typeName = [
        self::ROLE_ADMIN => 'ROLE_ADMIN',
        self::ROLE_USER => 'ROLE_USER',
        self::ROLE_COURSE_COMMITEE => 'COURSE_COMMITEE',
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
            self::ROLE_ADMIN,
            self::ROLE_USER,
            self::ROLE_COURSE_COMMITEE,
        ];
    }
}
