<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints\ConditionAuthor;
use Doctrine\ORM\Mapping as ORM;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\NotesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ConditionAuthor(groups={"post_note", "put_note"})
 */
class Notes
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_note", "get_notes", "get_question", "get_questions"
     * })
     */
    private $id;

    /**
     * @var Questions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="note")
     * @Annotation\Groups({
     *     "get_note", "get_notes", "post_note", "put_note"
     * })
     * @Annotation\Type("AppBundle\Entity\Questions")
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $questions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="note")
     * @Annotation\Groups({
     *     "post_note", "put_note"
     * })
     * @Annotation\Type("AppBundle\Entity\User")
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $user;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin", inversedBy="note")
     * @Annotation\Groups({
     *     "post_note", "put_note"
     * })
     * @Annotation\Type("AppBundle\Entity\Admin")
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $admin;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *     "get_note", "get_notes", "post_note", "put_note", "get_question", "get_questions"
     * })
     * @Assert\NotBlank(groups={"post_note", "put_note"})
     * @Assert\Length(
     *     groups={"post_note", "put_note"},
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     */
    private $text;

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
     * Set text.
     *
     * @param string $text
     *
     * @return Notes
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set questions.
     *
     * @param \AppBundle\Entity\Questions $questions
     *
     * @return Notes
     */
    public function setQuestions(\AppBundle\Entity\Questions $questions = null)
    {
        $this->questions = $questions;

        return $this;
    }

    /**
     * Get questions.
     *
     * @return \AppBundle\Entity\Questions
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Notes
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set admin.
     *
     * @param \AppBundle\Entity\Admin $admin
     *
     * @return Notes
     */
    public function setAdmin(\AppBundle\Entity\Admin $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin.
     *
     * @return \AppBundle\Entity\Admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("created_at")
     * @Annotation\Groups({"get_note", "get_notes"})
     */
    public function getSerializedCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("DateTime<'Y-m-d H:i:s'>")
     * @Annotation\SerializedName("updated_at")
     * @Annotation\Groups({"get_note", "get_notes"})
     */
    public function getSerializedUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("array")
     * @Annotation\SerializedName("author")
     * @Annotation\Groups({"get_note", "get_notes", "get_question", "get_questions"})
     */
    public function getSerializedAuthor()
    {
        $result = [];
        if ($this->getUser()) {
            $result['user']['id'] = $this->getUser()->getId();
            $result['user']['name'] = $this->getUser()->getUsername();
        } elseif ($this->getAdmin()) {
            $result['admin']['id'] = $this->getAdmin()->getId();
            $result['admin']['name'] = $this->getAdmin()->getUsername();
        }

        return $result;
    }
}
