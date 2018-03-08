<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotEqualTo;

/**
 * @Annotation
 */
class ConditionQuestion extends NotEqualTo
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
