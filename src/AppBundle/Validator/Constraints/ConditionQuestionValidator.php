<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\Request\UserQuestionAnswerTestRequestModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionQuestionValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        $result = [];
        /** @var UserQuestionAnswerTestRequestModel $entity */
        foreach ($entity->getAnswers() as $answer) {
            $result[] = $answer->getQuestionAnswers()->getQuestions()->getId();
        }

        if (count(array_unique($result)) > 1) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ compared_value }}', implode(',', $result))
                ->atPath('questions')
                ->addViolation();
        }
    }
}
