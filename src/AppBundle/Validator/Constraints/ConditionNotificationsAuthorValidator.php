<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\Request\NotificationsRequestModel;
use AppBundle\Model\Request\UserQuestionAnswerTestRequestModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionNotificationsAuthorValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        if ($entity->getUser()) {

        /** @var NotificationsRequestModel $entity */
        foreach ($entity->getNotifications() as $notification) {
            if ($entity->getUser() !== $notification->getUser()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ compared_value }}', $notification->getUser()->getId())
                    ->atPath('user')
                    ->addViolation();
            }
        }

        }
    }
}
