<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotEqualTo;

/**
 * @Annotation
 */
class ConditionRepeatedQuestions extends NotEqualTo
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
