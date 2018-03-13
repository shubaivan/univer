<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Enum\ImprovementSuggestionStatusEnum;
use AppBundle\Entity\Enum\ProviderTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="notifications")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\NotificationsRepository")
 */
class Notifications
{
    use TraitTimestampable;
    const GROUP_POST_NOTIFICATION = 'post_notifications';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_notifications"
     * })
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="favorites")
     * @Assert\NotBlank(groups={"post_notifications"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "post_notifications", "get_notifications"
     * })
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="favorites")
     * @Assert\NotBlank(groups={"post_notifications"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "post_notifications", "get_notifications"
     * })
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"post_notifications"})
     * @Assert\Length(groups={"post_notifications"}, min=2, max=255)
     * @Annotation\Groups({
     *     "post_notifications", "get_notifications"
     * })
     * @Annotation\Accessor(setter="setAccessorProvider")
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(groups={"post_notifications"}, min=2, max=255)
     * @Annotation\Groups({
     *     "post_notifications", "get_notifications"
     * })
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $provider
     */
    public function setAccessorProvider($provider = null)
    {
        $this->setProvider($provider);
    }

    /**
     * Set provider.
     *
     * @param string $provider
     *
     * @return Notifications
     */
    public function setProvider($provider = null)
    {
        if (!in_array($provider, ProviderTypeEnum::getAvailableTypes(), true)) {
            throw new \InvalidArgumentException(
                'Invalid type. Available type: '.implode(',', ProviderTypeEnum::getAvailableTypes())
            );
        }

        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider.
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set message.
     *
     * @param null|string $message
     *
     * @return Notifications
     */
    public function setMessage($message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status.
     *
     * @param null|string $status
     *
     * @return Notifications
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
     * Get status.
     *
     * @return null|string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user.
     *
     * @param null|\AppBundle\Entity\User $user
     *
     * @return Notifications
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return null|\AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sender.
     *
     * @param null|\AppBundle\Entity\User $sender
     *
     * @return Notifications
     */
    public function setSender(\AppBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender.
     *
     * @return null|\AppBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (!$this->getStatus()) {
            $this->setStatus(ImprovementSuggestionStatusEnum::NOT_VIEWED);
        }
    }

    public static function getPostGroup()
    {
        return [self::GROUP_POST_NOTIFICATION];
    }
}
