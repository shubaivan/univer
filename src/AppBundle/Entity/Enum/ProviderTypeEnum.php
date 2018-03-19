<?php

namespace AppBundle\Entity\Enum;

abstract class ProviderTypeEnum
{
    const TYPE_PROVIDER_COMMENT = 'comment';
    const TYPE_PROVIDER_QUESTION_ANSWER_TEST = 'question_answer_test';

    /** @var array user friendly named type */
    protected static $typeName = [
        self::TYPE_PROVIDER_COMMENT => 'comment',
        self::TYPE_PROVIDER_QUESTION_ANSWER_TEST => 'question_answer_test',
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
            self::TYPE_PROVIDER_QUESTION_ANSWER_TEST,
        ];
    }
}
