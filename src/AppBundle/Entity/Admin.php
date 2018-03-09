<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @Annotation\Groups({
     *      "profile"
     * })
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

    /**
     * @var ArrayCollection|Notes[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notes", mappedBy="admin", cascade={"persist"})
     */
    private $note;

    /**
     * @var ArrayCollection|Questions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Questions", mappedBy="admin", cascade={"persist"})
     */
    private $questions;

    /**
     * @var ArrayCollection|Comments[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comments", mappedBy="admin", cascade={"persist"})
     */
    private $comments;

    /**
     * @var ArrayCollection|Events[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Events", mappedBy="admin", cascade={"persist", "remove"})
     */
    private $events;

    public function __construct()
    {
        $this->note = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

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
     * @Annotation\SerializedName("roles")
     * @Annotation\Groups({"profile"})
     */
    public function getSerializedRole()
    {
        return $this->getRoles();
    }

    /**
     * Add note.
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return Admin
     */
    public function addNote(\AppBundle\Entity\Notes $note)
    {
        $this->note[] = $note;

        return $this;
    }

    /**
     * Remove note.
     *
     * @param \AppBundle\Entity\Notes $note
     */
    public function removeNote(\AppBundle\Entity\Notes $note)
    {
        $this->note->removeElement($note);
    }

    /**
     * Get note.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Add question.
     *
     * @param \AppBundle\Entity\Notes $question
     *
     * @return Admin
     */
    public function addQuestion(\AppBundle\Entity\Notes $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question.
     *
     * @param \AppBundle\Entity\Notes $question
     */
    public function removeQuestion(\AppBundle\Entity\Notes $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Add comment.
     *
     * @param \AppBundle\Entity\Comments $comment
     *
     * @return Admin
     */
    public function addComment(\AppBundle\Entity\Comments $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param \AppBundle\Entity\Comments $comment
     */
    public function removeComment(\AppBundle\Entity\Comments $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add event.
     *
     * @param \AppBundle\Entity\Events $event
     *
     * @return Admin
     */
    public function addEvent(\AppBundle\Entity\Events $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event.
     *
     * @param \AppBundle\Entity\Events $event
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEvent(\AppBundle\Entity\Events $event)
    {
        return $this->events->removeElement($event);
    }

    /**
     * Get events.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
