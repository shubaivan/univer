<?php

namespace AppBundle\Entity\Enum;

use AppBundle\Entity\Comments;
use AppBundle\Entity\Questions;
use AppBundle\Entity\UserQuestionAnswerTest;

abstract class ProviderTypeEnum
{
    const TYPE_PROVIDER_COMMENT = Comments::class;
    const TYPE_PROVIDER_QUESTIONS = Questions::class;
    const TYPE_PROVIDER_QUESTION_ANSWER_TEST = UserQuestionAnswerTest::class;

    /** @var array user friendly named type */
    protected static $typeName = [
        self::TYPE_PROVIDER_COMMENT => 1,
        self::TYPE_PROVIDER_QUESTION_ANSWER_TEST => 2,
        self::TYPE_PROVIDER_QUESTIONS => 3,
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
