<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\RepeatedQuestions;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionRepeatedQuestionsValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        /** @var RepeatedQuestions $entity */
        if ($entity->getQuestionsOrigin() === $entity->getQuestionsRepeated()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ compared_value }}', 'each other')
                ->atPath('questionsOrigin, questionsRepeated')
                ->addViolation();
        }
    }
}
