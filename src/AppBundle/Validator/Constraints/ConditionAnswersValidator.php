<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\Request\UserQuestionAnswerTestRequestModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionAnswersValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        /** @var UserQuestionAnswerTestRequestModel $entity */
        if (!$entity->getAnswers()->count()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('answers')
                ->addViolation();
        }
    }
}
