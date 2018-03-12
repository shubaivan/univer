<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Enum\ImprovementSuggestionStatusEnum;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="notifications")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\NotificationsRepository")
 */
class Notifications
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_favorite", "get_favorites"
     * })
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="favorites")
     * @Assert\NotBlank(groups={"post_favorite", "put_favorite"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "get_favorite", "get_favorites", "post_favorite", "put_favorite"
     * })
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="favorites")
     * @Assert\NotBlank(groups={"post_favorite", "put_favorite"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "get_favorite", "get_favorites", "post_favorite", "put_favorite"
     * })
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"admin_post_user"})
     * @Assert\Length(groups={"put_user"}, min=2, max=255)
     * @Annotation\Groups({
     *      "profile", "put_user", "admin_post_user", "admin_put_user"
     * })
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(groups={"put_user"}, min=2, max=255)
     * @Annotation\Groups({
     *      "profile", "put_user", "admin_post_user", "admin_put_user"
     * })
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"admin_post_user"})
     * @Assert\Length(groups={"put_user"}, min=2, max=255)
     * @Annotation\Groups({
     *      "profile", "put_user", "admin_post_user", "admin_put_user"
     * })
     * @Annotation\Accessor(setter="setSerializedAccessorStatus")
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
     * Set provider.
     *
     * @param string $provider
     *
     * @return Notifications
     */
    public function setProvider($provider)
    {
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
     * @param string|null $message
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
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status.
     *
     * @param string|null $status
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
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
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
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sender.
     *
     * @param \AppBundle\Entity\User|null $sender
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
     * @return \AppBundle\Entity\User|null
     */
    public function getSender()
    {
        return $this->sender;
    }
}
