<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\Request\NotificationsRequestModel;
use AppBundle\Model\Request\UserQuestionAnswerTestRequestModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionNotificationsValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        /** @var NotificationsRequestModel $entity */
        if (!$entity->getNotifications()->count()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('notifications')
                ->addViolation();
        }
    }
}
