<?php

namespace AppBundle\Model\Request;

use AppBundle\Entity\Enum\ImprovementSuggestionStatusEnum;
use AppBundle\Entity\Notifications;
use AppBundle\Entity\User;
use AppBundle\Entity\UserQuestionAnswerTest;
use AppBundle\Validator\Constraints\ConditionAuthor;
use AppBundle\Validator\Constraints\ConditionNotifications;
use AppBundle\Validator\Constraints\ConditionNotificationsAuthor;
use AppBundle\Validator\Constraints\ConditionQuestion;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class NotificationsRequestModel
 * @package AppBundle\Model\Request
 * @ConditionNotifications(groups={"post_notification_model"})
 * @ConditionNotificationsAuthor(value="author id", groups={"post_notification_model"})
 */
class NotificationsRequestModel
{
    const GROUP_POST = 'post_notification_model';

    /**
     * @var ArrayCollection|Notifications[]
     *
     * @Annotation\Groups({
     *     "post_notification_model", "get_notifications"
     * })
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\Notifications>")
     */
    private $notifications;

    /**
     * @var User
     *
     * @Annotation\Groups({
     *     "post_notification_model"
     * })
     * @Annotation\Type("AppBundle\Entity\User")
     */
    private $user;

    /**
     * @var string
     *
     * @Annotation\Groups({
     *     "post_notification_model", "get_notifications"
     * })
     * @Assert\NotBlank(groups={"post_notification_model"})
     * @Annotation\Type("string")
     * @Annotation\Accessor(setter="setAccessorStatus")
     */
    private $status;

    /**
     * @return ArrayCollection|Notifications[]
     */
    public function getNotifications()
    {
        return $this->notifications ? $this->notifications : new ArrayCollection();
    }

    /**
     * @param ArrayCollection|Notifications[] $notifications
     *
     * @return $this
     */
    public function setNotifications($notifications)
    {
        $this->getNotifications()->clear();
        foreach ($notifications as $answer) {
            $this->addNotifications($answer);
        }

        return $this;
    }

    public function addNotifications(Notifications $answerTest)
    {
        if (!$this->getNotifications()->contains($answerTest)) {
            $this->notifications[] = $answerTest;
        }
    }

    /**
     * @return array
     */
    public static function getPostGroup()
    {
        return [self::GROUP_POST];
    }

    public function setAccessorStatus($status = null)
    {
        $this->setStatus($status);
    }

    /**
     * @param null $status
     * @return $this
     */
    public function setStatus($status = null)
    {
        if (!in_array($status, ImprovementSuggestionStatusEnum::getAvailableTypes(), true)) {
            throw new \InvalidArgumentException(
                'Invalid type. Available type: '.implode(',', ImprovementSuggestionStatusEnum::getAvailableTypes())
            );
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
