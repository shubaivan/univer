<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @AssertBridge\UniqueEntity(
 *     groups={"registration", "admin_post_user"},
 *     fields="username",
 *     errorPath="not valid"
 * )
 * @AssertBridge\UniqueEntity(
 *     groups={"registration", "admin_post_user"},
 *     fields="email",
 *     errorPath="not valid"
 * )
 */
class User extends AbstractUser implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *      "profile", "get_question", "get_questions", "get_notes",
     *     "get_favorite", "get_favorites", "get_user_question_answer_test",
     *     "get_events", "get_repeated_questions", "get_improvement_suggestions",
     *     "get_questions_corrections", "get_question_corrections"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Annotation\Groups({
     *      "profile", "put_user", "registration", "admin_post_user", "admin_put_user",
     *      "get_questions_corrections", "get_question_corrections", "put_user", "get_events",
     *      "get_improvement_suggestions"
     * })
     * @Annotation\SerializedName("_username")
     * @Assert\NotBlank(groups={"registration", "admin_post_user", "put_user"})
     */
    private $username;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Annotation\Groups({
     *      "profile", "admin_post_user", "admin_put_user", "put_user"
     * })
     * @Annotation\Accessor(setter="setIsActiveAccessor")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Annotation\Groups({
     *      "profile", "registration", "put_user", "admin_post_user",
     *      "admin_put_user", "put_user"
     * })
     * @Annotation\SerializedName("_email")
     * @Annotation\Accessor(setter="setEmailAccessor")
     * @Assert\Email(
     *     groups={"registration", "put_user", "admin_post_user"},
     *     checkMX = false
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank(groups={"registration", "admin_post_user", "put_user"})
     * @Annotation\Groups({
     *      "registration", "admin_post_user", "admin_put_user", "put_user"
     * })
     * @Annotation\SerializedName("_password")
     * @Assert\Length(
     *     groups={"registration", "put_user"},
     *      min = 5,
     *      max = 100
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"admin_post_user", "put_user"})
     * @Assert\Length(groups={"put_user", "put_user"}, min=2, max=255)
     * @Annotation\Groups({
     *      "profile", "put_user", "admin_post_user", "admin_put_user", "put_user"
     * })
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"admin_post_user", "put_user"})
     * @Annotation\Groups({
     *      "profile", "put_user", "admin_post_user", "admin_put_user", "put_user"
     * })
     */
    private $lastName;

    /**
     * @var int
     *
     * @ORM\Column(name="student_id", type="integer", nullable=true)
     * @Annotation\Groups({
     *      "profile", "put_user", "admin_post_user", "admin_put_user"
     * })
     */
    private $studentId;

    /**
     * @var int
     *
     * @ORM\Column(name="year_of_graduation", type="integer", nullable=true)
     * @Annotation\Groups({
     *      "profile", "put_user", "admin_post_user", "admin_put_user", "put_user"
     * })
     */
    private $yearOfGraduation;

    /**
     * @var ArrayCollection|UserQuestionAnswerOpen[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerOpen", mappedBy="user", cascade={"persist"})
     */
    private $userQuestionAnswerOpen;

    /**
     * @var ArrayCollection|UserQuestionAnswerTest[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerTest", mappedBy="user", cascade={"persist", "remove"})
     */
    private $userQuestionAnswerTest;

    /**
     * @var Appeals[]|ArrayCollection
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Favorites", mappedBy="user", cascade={"persist", "remove"})
     */
    private $favorites;

    /**
     * @var ArrayCollection|Comments[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comments", mappedBy="user", cascade={"persist"})
     */
    private $comments;

    /**
     * @var Role The role
     *
     * @ORM\ManyToMany(targetEntity="Role", cascade={"persist"})
     * @ORM\JoinTable(name="users_roles")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Annotation\Groups({
     *     "admin_post_user", "admin_put_user"
     * })
     * @Annotation\Type("ArrayCollection<AppBundle\Entity\Role>")
     * @Annotation\SerializedName("roles")
     */
    private $userRoles;

    /**
     * @var CoursesOfStudy
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CoursesOfStudy", inversedBy="users")
     * @Annotation\Type("AppBundle\Entity\CoursesOfStudy")
     * @Annotation\Groups({
     *     "profile", "put_user", "admin_post_user", "admin_put_user", "put_user", "get_user", "registration"
     * })
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $coursesOfStudy;

    /**
     * @var ArrayCollection|RepeatedQuestions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RepeatedQuestions", mappedBy="user", cascade={"persist", "remove"})
     */
    private $repeatedQuestions;

    /**
     * @var ArrayCollection|UserQuestionAnswerResult[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserQuestionAnswerResult", mappedBy="user", cascade={"persist", "remove"})
     */
    private $userQuestionAnswerResult;

    /**
     * @var ArrayCollection|Events[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Events", mappedBy="user", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var ArrayCollection|ImprovementSuggestions[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ImprovementSuggestions", mappedBy="user", cascade={"persist", "remove"})
     */
    private $improvementSuggestions;

    /**
     * @var ArrayCollection|QuestionCorrections[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\QuestionCorrections", mappedBy="user", cascade={"persist", "remove"})
     */
    private $questionCorrections;

    /**
     * @var ArrayCollection|Notifications[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notifications", mappedBy="user", cascade={"persist", "remove"})
     */
    private $userNotifications;

    /**
     * @var ArrayCollection|Notifications[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Notifications", mappedBy="sender", cascade={"persist", "remove"})
     */
    private $senderNotifications;

    /**
     * @var ArrayCollection|Votes[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Votes", mappedBy="user", cascade={"persist", "remove"})
     */
    private $votes;

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
        $this->userRoles = new ArrayCollection();
        $this->repeatedQuestions = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->improvementSuggestions = new ArrayCollection();
        $this->questionCorrections = new ArrayCollection();
        $this->userNotifications = new ArrayCollection();
        $this->senderNotifications = new ArrayCollection();
        $this->votes = new ArrayCollection();
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
        $rolesEntities = $this->getUserRoles();
        $roles = [];
        foreach ($rolesEntities as $role) {
            $roles[] = $role->getName();
        }

        return array_unique($roles);
    }

    /**
     * Gets all roles.
     *
     * @return ArrayCollection|Role
     */
    public function getUserRoles()
    {
        if (!$this->userRoles) {
            $this->userRoles = new ArrayCollection();
        }

        return $this->userRoles;
    }

    /**
     * Adds the given role.
     *
     * @param Role $role The role object
     *
     * @return $this
     */
    public function addUserRole(Role $role)
    {
        if (!$this->getUserRoles()->contains($role)) {
            $this->getUserRoles()->add($role);
        }

        return $this;
    }

    /**
     * Removes the given role.
     *
     * @param Role $role The role object
     *
     * @return $this
     */
    public function removeUserRole(Role $role)
    {
        $this->getUserRoles()->remove($role);

        return $this;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Set coursesOfStudy.
     *
     * @param null|\AppBundle\Entity\CoursesOfStudy $coursesOfStudy
     *
     * @return User
     */
    public function setCoursesOfStudy(\AppBundle\Entity\CoursesOfStudy $coursesOfStudy = null)
    {
        $this->coursesOfStudy = $coursesOfStudy;

        return $this;
    }

    /**
     * Get coursesOfStudy.
     *
     * @return null|\AppBundle\Entity\CoursesOfStudy
     */
    public function getCoursesOfStudy()
    {
        return $this->coursesOfStudy;
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
     * @param null $isActive
     */
    public function setIsActiveAccessor($isActive = null)
    {
        if (null === $isActive) {
            $this->setIsActive(true);
        } else {
            $this->setIsActive($isActive);
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
     * Add userQuestionAnswerOpen.
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
     * Remove userQuestionAnswerOpen.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerOpen $userQuestionAnswerOpen
     */
    public function removeUserQuestionAnswerOpen(\AppBundle\Entity\UserQuestionAnswerOpen $userQuestionAnswerOpen)
    {
        $this->userQuestionAnswerOpen->removeElement($userQuestionAnswerOpen);
    }

    /**
     * Get userQuestionAnswerOpen.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserQuestionAnswerOpen()
    {
        return $this->userQuestionAnswerOpen;
    }

    /**
     * Add userQuestionAnswerTest.
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
     * Remove userQuestionAnswerTest.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest
     */
    public function removeUserQuestionAnswerTest(\AppBundle\Entity\UserQuestionAnswerTest $userQuestionAnswerTest)
    {
        $this->userQuestionAnswerTest->removeElement($userQuestionAnswerTest);
    }

    /**
     * Get userQuestionAnswerTest.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserQuestionAnswerTest()
    {
        return $this->userQuestionAnswerTest;
    }

    /**
     * Add appeal.
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
     * Remove appeal.
     *
     * @param \AppBundle\Entity\Appeals $appeal
     */
    public function removeAppeal(\AppBundle\Entity\Appeals $appeal)
    {
        $this->appeals->removeElement($appeal);
    }

    /**
     * Get appeals.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppeals()
    {
        return $this->appeals;
    }

    /**
     * Add question.
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
     * Remove question.
     *
     * @param \AppBundle\Entity\Questions $question
     */
    public function removeQuestion(\AppBundle\Entity\Questions $question)
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
     * Add report.
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
     * Remove report.
     *
     * @param \AppBundle\Entity\Reports $report
     */
    public function removeReport(\AppBundle\Entity\Reports $report)
    {
        $this->report->removeElement($report);
    }

    /**
     * Get report.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Add note.
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
     * Add favorite.
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
     * Remove favorite.
     *
     * @param \AppBundle\Entity\Favorites $favorite
     */
    public function removeFavorite(\AppBundle\Entity\Favorites $favorite)
    {
        $this->favorites->removeElement($favorite);
    }

    /**
     * Get favorites.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * Add comment.
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
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"profile", "registration"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"profile", "registration"})
     */
    public function getSerializedUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add repeatedQuestion.
     *
     * @param \AppBundle\Entity\RepeatedQuestions $repeatedQuestion
     *
     * @return User
     */
    public function addRepeatedQuestion(\AppBundle\Entity\RepeatedQuestions $repeatedQuestion)
    {
        $this->repeatedQuestions[] = $repeatedQuestion;

        return $this;
    }

    /**
     * Remove repeatedQuestion.
     *
     * @param \AppBundle\Entity\RepeatedQuestions $repeatedQuestion
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeRepeatedQuestion(\AppBundle\Entity\RepeatedQuestions $repeatedQuestion)
    {
        return $this->repeatedQuestions->removeElement($repeatedQuestion);
    }

    /**
     * Get repeatedQuestions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRepeatedQuestions()
    {
        return $this->repeatedQuestions;
    }

    /**
     * Add userQuestionAnswerResult.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult
     *
     * @return User
     */
    public function addUserQuestionAnswerResult(\AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult)
    {
        $this->userQuestionAnswerResult[] = $userQuestionAnswerResult;

        return $this;
    }

    /**
     * Remove userQuestionAnswerResult.
     *
     * @param \AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeUserQuestionAnswerResult(\AppBundle\Entity\UserQuestionAnswerResult $userQuestionAnswerResult)
    {
        return $this->userQuestionAnswerResult->removeElement($userQuestionAnswerResult);
    }

    /**
     * Get userQuestionAnswerResult.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserQuestionAnswerResult()
    {
        return $this->userQuestionAnswerResult;
    }

    /**
     * Add event.
     *
     * @param \AppBundle\Entity\Events $event
     *
     * @return User
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
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
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

    /**
     * Add improvementSuggestion.
     *
     * @param \AppBundle\Entity\ImprovementSuggestions $improvementSuggestion
     *
     * @return User
     */
    public function addImprovementSuggestion(\AppBundle\Entity\ImprovementSuggestions $improvementSuggestion)
    {
        $this->improvementSuggestions[] = $improvementSuggestion;

        return $this;
    }

    /**
     * Remove improvementSuggestion.
     *
     * @param \AppBundle\Entity\ImprovementSuggestions $improvementSuggestion
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeImprovementSuggestion(\AppBundle\Entity\ImprovementSuggestions $improvementSuggestion)
    {
        return $this->improvementSuggestions->removeElement($improvementSuggestion);
    }

    /**
     * Get improvementSuggestions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImprovementSuggestions()
    {
        return $this->improvementSuggestions;
    }

    /**
     * Add questionCorrection.
     *
     * @param \AppBundle\Entity\QuestionCorrections $questionCorrection
     *
     * @return User
     */
    public function addQuestionCorrection(\AppBundle\Entity\QuestionCorrections $questionCorrection)
    {
        $this->questionCorrections[] = $questionCorrection;

        return $this;
    }

    /**
     * Remove questionCorrection.
     *
     * @param \AppBundle\Entity\QuestionCorrections $questionCorrection
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeQuestionCorrection(\AppBundle\Entity\QuestionCorrections $questionCorrection)
    {
        return $this->questionCorrections->removeElement($questionCorrection);
    }

    /**
     * Get questionCorrections.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionCorrections()
    {
        return $this->questionCorrections;
    }

    /**
     * Add userNotification.
     *
     * @param \AppBundle\Entity\Notifications $userNotification
     *
     * @return User
     */
    public function addUserNotification(\AppBundle\Entity\Notifications $userNotification)
    {
        $this->userNotifications[] = $userNotification;

        return $this;
    }

    /**
     * Remove userNotification.
     *
     * @param \AppBundle\Entity\Notifications $userNotification
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeUserNotification(\AppBundle\Entity\Notifications $userNotification)
    {
        return $this->userNotifications->removeElement($userNotification);
    }

    /**
     * Get userNotifications.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserNotifications()
    {
        return $this->userNotifications;
    }

    /**
     * Add senderNotification.
     *
     * @param \AppBundle\Entity\Notifications $senderNotification
     *
     * @return User
     */
    public function addSenderNotification(\AppBundle\Entity\Notifications $senderNotification)
    {
        $this->senderNotifications[] = $senderNotification;

        return $this;
    }

    /**
     * Remove senderNotification.
     *
     * @param \AppBundle\Entity\Notifications $senderNotification
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeSenderNotification(\AppBundle\Entity\Notifications $senderNotification)
    {
        return $this->senderNotifications->removeElement($senderNotification);
    }

    /**
     * Get senderNotifications.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSenderNotifications()
    {
        return $this->senderNotifications;
    }

    /**
     * Add vote.
     *
     * @param \AppBundle\Entity\Votes $vote
     *
     * @return User
     */
    public function addVote(\AppBundle\Entity\Votes $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote.
     *
     * @param \AppBundle\Entity\Votes $vote
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeVote(\AppBundle\Entity\Votes $vote)
    {
        return $this->votes->removeElement($vote);
    }

    /**
     * Get votes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
