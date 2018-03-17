<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Annotation
 */
class ConditionQuestionAnswers extends NotBlank
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
