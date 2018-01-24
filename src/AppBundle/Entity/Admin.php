<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Admin.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdminRepository")
 */
class Admin extends AbstractUser implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     * @Annotation\Groups({
     *      "profile"
     * })
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     * @Annotation\Groups({
     *      "profile"
     * })
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     * @Annotation\Groups({
     *      "profile"
     * })
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Annotation\Groups({
     *      "profile"
     * })
     */
    private $email;

    // --------------------------------implements methods--------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return [self::ROLE_ADMIN];
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

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
     * Set username.
     *
     * @param string $username
     *
     * @return Admin
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Admin
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return Admin
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return Admin
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Admin
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("array<string>")
     * @Annotation\SerializedName("role_ids")
     * @Annotation\Groups({"profile"})
     */
    public function getSerializedRole()
    {
        return $this->getRoles();
    }
}
