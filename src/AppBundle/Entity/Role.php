<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RoleRepository")
 * @ORM\Table(name="roles")
 * @ORM\HasLifecycleCallbacks
 * @AssertBridge\UniqueEntity(
 *     groups={"admin_post_user", "admin_put_user", "admin_post_role", "admin_put_role"},
 *     fields="name",
 *     errorPath="not valid",
 *     message="This name is already in use."
 * )
 */
class Role
{
    use TraitTimestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Annotation\Groups({
     *     "admin_post_user", "admin_put_user", "get_roles", "profile"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Annotation\Groups({
     *     "admin_post_user", "get_roles", "admin_post_role", "profile", "admin_put_role"
     * })
     * @Assert\NotBlank(groups={"admin_post_user", "admin_post_role", "admin_put_role"})
     * @Annotation\Accessor(setter="setNameAccessor")
     */
    private $name;

    /**
     * Gets the ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the role name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the role name.
     *
     * @param string $name The role name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = mb_strtoupper($name);

        return $this;
    }

    public function setNameAccessor(string $name)
    {
        $this->setName($name);
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"get_roles"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_roles"})
     */
    public function getSerializedUpdatedAt()
    {
        return $this->updatedAt;
    }
}
