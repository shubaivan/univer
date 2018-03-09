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
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ImprovementSuggestionsRepository")
 */
class ImprovementSuggestions
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_improvement_suggestions"
     * })
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="improvementSuggestions")
     * @Assert\NotBlank(groups={"post_improvement_suggestions"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "get_improvement_suggestions", "post_improvement_suggestions"
     * })
     */
    private $user;

    /**
     * @ORM\Column(type="text", length=65000, nullable=true)
     * @Annotation\Groups({
     *     "get_improvement_suggestions", "post_improvement_suggestions"
     * })
     * @Assert\NotBlank(groups={"post_improvement_suggestions"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Annotation\Groups({
     *     "get_improvement_suggestions", "post_improvement_suggestions"
     * })
     * @Annotation\Accessor(setter="setSerializedAccessorStatus")
     */
    private $status = ImprovementSuggestionStatusEnum::NOT_VIEWED;


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
     * Set description.
     *
     * @param string|null $description
     *
     * @return ImprovementSuggestions
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return ImprovementSuggestions
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
     * @return ImprovementSuggestions
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

    public function setSerializedAccessorStatus($status)
    {
        $this->setStatus($status);
    }
}
