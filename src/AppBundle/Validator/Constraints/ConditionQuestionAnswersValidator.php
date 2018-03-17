<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\QuestionAnswers;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionQuestionAnswersValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        /** @var QuestionAnswers $entity */
        if (!$entity->getQuestions() && !$entity->getQuestionCorrections()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('questions')
                ->addViolation();

            $this->context->buildViolation($constraint->message)
                ->atPath('questionCorrections')
                ->addViolation();
        }
    }
}
