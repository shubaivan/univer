<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Annotation
 */
class ConditionAuthor extends NotBlank
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
