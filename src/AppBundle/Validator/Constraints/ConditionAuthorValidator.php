<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionAuthorValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        if (!$entity->getUser() && !$entity->getAdmin()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('author')
                ->addViolation();
        }
    }
}
