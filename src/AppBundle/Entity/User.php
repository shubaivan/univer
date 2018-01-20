<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @AssertBridge\UniqueEntity(
 *     groups={"registration"},
 *     fields="username",
 *     errorPath="not valid",
 *     message="This username is already in use."
 * )
 * @AssertBridge\UniqueEntity(
 *     groups={"registration"},
 *     fields="email",
 *     errorPath="not valid",
 *     message="This email is already in use."
 * )
 */
class User extends AbstractUser implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *      "profile"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Annotation\Groups({
     *      "profile", "put_user", "registration"
     * })
     * @Annotation\SerializedName("_username")
     * @Assert\NotBlank(groups={"registration"})
     */
    private $username;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Annotation\Groups({
     *      "profile"
     * })
     * @Annotation\Accessor(setter="setIsActiveAccessor")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Annotation\Groups({
     *      "profile", "registration", "put_user"
     * })
     * @Assert\NotBlank(groups={"registration", "put_user"})
     * @Annotation\SerializedName("_email")
     * @Annotation\Accessor(setter="setEmailAccessor")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank(groups={"registration"})
     * @Annotation\Groups({
     *      "registration"
     * })
     * @Annotation\SerializedName("_password")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Assert\Length(groups={"put_user"}, min=2, max=255)
     * @Annotation\Groups({
     *      "profile", "put_user"
     * })
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Annotation\Groups({
     *      "profile", "put_user"
     * })
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="student_id", type="integer", nullable=true)
     * @Annotation\Groups({
     *      "profile", "put_user"
     * })
     */
    private $studentId;

    /**
     * @var string
     *
     * @ORM\Column(name="year_of_graduation", type="integer", nullable=true)
     * @Annotation\Groups({
     *      "profile", "put_user"
     * })
     */
    private $yearOfGraduation;

    /**
     * @var array
     *
     * @ORM\Column(name="role", type="array", length=25, nullable=false)
     * @Annotation\Groups({
     *      "profile"
     * })
     */
    private $roles = [];

    /**
     * @var ArrayCollection|UserQuestionAnswerOpen[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerOpen", mappedBy="user", cascade={"persist"})
     */
    private $userQuestionAnswerOpen;

    /**
     * @var ArrayCollection|UserQuestionAnswerTest[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerTest", mappedBy="user", cascade={"persist"})
     */
    private $userQuestionAnswerTest;

    /**
     * @var ArrayCollection|Appeals[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Appeals", mappedBy="user", cascade={"persist"})
     */
    private $appeals;

    /**
     * @var ArrayCollection|Questions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Questions", mappedBy="user", cascade={"persist"})
     */
    private $questions;

    /**
     * @var ArrayCollection|Reports[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Reports", mappedBy="user", cascade={"persist"})
     */
    private $report;

    /**
     * @var ArrayCollection|Notes[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notes", mappedBy="user", cascade={"persist"})
     */
    private $note;

    /**
     * @var ArrayCollection|Favorites[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Favorites", mappedBy="user", cascade={"persist"})
     */
    private $favorites;

    /**
     * @var ArrayCollection|Comments[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comments", mappedBy="user", cascade={"persist"})
     */
    private $comments;

    /**
     * User constructor.
     *
     * @param $username
     */
    public function __construct($username)
    {
        $this->isActive = true;
        $this->username = $username;
        $this->roles = new ArrayCollection();
        $this->userQuestionAnswerOpen = new ArrayCollection();
        $this->userQuestionAnswerTest = new ArrayCollection();
        $this->appeals = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->report = new ArrayCollection();
        $this->note = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setIsActiveAccessor();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the user roles.
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole($role)
    {
        if (count($this->roles) > 1 && false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function eraseCredentials()
    {
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
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @param null $inviteCode
     */
    public function setIsActiveAccessor($inviteCode = null)
    {
        if (null === $this->isActive && $inviteCode) {
            $this->setIsActive($inviteCode);
        } elseif (null === $this->isActive) {
            $this->setIsActive(true);
        }
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $email
     */
    public function setEmailAccessor($email)
    {
        $this->setEmail($email);
        if (!$this->username) {
            $this->username = $email;
        }
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
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return User
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
     * @return User
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
     * Set studentId.
     *
     * @param int $studentId
     *
     * @return User
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;

        return $this;
    }

    /**
     * Get studentId.
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * Set yearOfGraduation.
     *
     * @param int $yearOfGraduation
     *
     * @return User
     */
    public function setYearOfGraduation($yearOfGraduation)
    {
        $this->yearOfGraduation = $yearOfGraduation;

        return $this;
    }

    /**
     * Get yearOfGraduation.
     *
     * @return int
     */
    public function getYearOfGraduation()
    {
        return $this->yearOfGraduation;
    }

    /**
     * Add userQuestionAnswerOpen
     *
     * @param \AppBundle\Entity\UserQuestionAnswerOpen $userQuestionAnswerOpen
     *
     * @return User
     */
    public function addUserQuestionAnswerOpen(\AppBundle\Entity\UserQuestionAnswerOpen $userQuestionAnswerOpen)
    {
        $this->userQuestionAnswerOpen[] = $userQuestionAnswerOpen;

        return $this;
    }

    /**
     * Remove userQuestionAnswerOpen
     *
     * @param \AppBundle\Entity\UserQuestionAnswerOpen $userQuestionAnswerOpen
     */
    public function removeUserQuestionAnswerOpen(\AppBundle\Entity\UserQuestionAnswerOpen $userQuestionAnswerOpen)
    {
        $this->userQuestionAnswerOpen->removeElement($userQuestionAnswerOpen);
    }

    /**
     * Get userQuestionAnswerOpen
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserQuestionAnswerOpen()
    {
        return $this->userQuestionAnswerOpen;
    }

    /**
     * Add userQuestionAnswerTest
     *
     * @param \AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest
     *
     * @return User
     */
    public function addUserQuestionAnswerTest(\AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest)
    {
        $this->userQuestionAnswerTest[] = $userQuestionAnswerTest;

        return $this;
    }

    /**
     * Remove userQuestionAnswerTest
     *
     * @param \AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest
     */
    public function removeUserQuestionAnswerTest(\AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest)
    {
        $this->userQuestionAnswerTest->removeElement($userQuestionAnswerTest);
    }

    /**
     * Get userQuestionAnswerTest
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserQuestionAnswerTest()
    {
        return $this->userQuestionAnswerTest;
    }

    /**
     * Add appeal
     *
     * @param \AppBundle\Entity\Appeals $appeal
     *
     * @return User
     */
    public function addAppeal(\AppBundle\Entity\Appeals $appeal)
    {
        $this->appeals[] = $appeal;

        return $this;
    }

    /**
     * Remove appeal
     *
     * @param \AppBundle\Entity\Appeals $appeal
     */
    public function removeAppeal(\AppBundle\Entity\Appeals $appeal)
    {
        $this->appeals->removeElement($appeal);
    }

    /**
     * Get appeals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppeals()
    {
        return $this->appeals;
    }

    /**
     * Add question
     *
     * @param \AppBundle\Entity\Questions $question
     *
     * @return User
     */
    public function addQuestion(\AppBundle\Entity\Questions $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param \AppBundle\Entity\Questions $question
     */
    public function removeQuestion(\AppBundle\Entity\Questions $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Add report
     *
     * @param \AppBundle\Entity\Reports $report
     *
     * @return User
     */
    public function addReport(\AppBundle\Entity\Reports $report)
    {
        $this->report[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param \AppBundle\Entity\Reports $report
     */
    public function removeReport(\AppBundle\Entity\Reports $report)
    {
        $this->report->removeElement($report);
    }

    /**
     * Get report
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return User
     */
    public function addNote(\AppBundle\Entity\Notes $note)
    {
        $this->note[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\Notes $note
     */
    public function removeNote(\AppBundle\Entity\Notes $note)
    {
        $this->note->removeElement($note);
    }

    /**
     * Get note
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Add favorite
     *
     * @param \AppBundle\Entity\Favorites $favorite
     *
     * @return User
     */
    public function addFavorite(\AppBundle\Entity\Favorites $favorite)
    {
        $this->favorites[] = $favorite;

        return $this;
    }

    /**
     * Remove favorite
     *
     * @param \AppBundle\Entity\Favorites $favorite
     */
    public function removeFavorite(\AppBundle\Entity\Favorites $favorite)
    {
        $this->favorites->removeElement($favorite);
    }

    /**
     * Get favorites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comments $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comments $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comments $comment
     */
    public function removeComment(\AppBundle\Entity\Comments $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}