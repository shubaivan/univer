<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RoleRepository")
 * @ORM\Table(name="roles")
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Annotation\Groups({
     *     "admin_post_user", "admin_put_user"
     * })
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Annotation\Groups({
     *     "admin_post_user"
     * })
     */
    protected $name;

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
        $this->name = $name;

        return $this;
    }
}
