<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\QuestionAnswers;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionPresentQuestionInAnswerValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        /** @var QuestionAnswers $entity */
        if ($entity->getQuestions() && $entity->getQuestionCorrections()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ compared_value }}', $entity->getQuestions()->getId().'at the same time')
                ->atPath('user')
                ->addViolation();

            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ compared_value }}', $entity->getQuestionCorrections()->getId().'at the same time')
                ->atPath('user')
                ->addViolation();
        }
    }
}
