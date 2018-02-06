<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints\ConditionAuthor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Evence\Bundle\SoftDeleteableExtensionBundle\Mapping\Annotation as Evence;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CommentsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ConditionAuthor(groups={"post_comment", "put_comment"})
 */
class Comments
{
    use TraitTimestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_comment", "get_comments"
     * })
     */
    private $id;

    /**
     * @var Questions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Questions", inversedBy="comments")
     * @Assert\NotBlank(groups={"post_comment", "put_comment"})
     * @Annotation\Type("AppBundle\Entity\Questions")
     * @Annotation\Groups({
     *     "get_comment", "get_comments", "post_comment", "put_comment"
     * })
     */
    private $questions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments")
     * @Evence\onSoftDelete(type="SET NULL")
     * @Assert\NotBlank(groups={"post_favorite", "put_favorite"})
     * @Annotation\Type("AppBundle\Entity\User")
     * @Annotation\Groups({
     *     "post_comment", "put_comment"
     * })
     */
    private $user;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin", inversedBy="comments")
     * @Annotation\Groups({
     *     "post_comment", "put_comment"
     * })
     * @Annotation\Type("AppBundle\Entity\Admin")
     * @Evence\onSoftDelete(type="SET NULL")
     */
    private $admin;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Annotation\Groups({
     *     "get_comment", "get_comments", "post_comment", "put_comment"
     * })
     */
    private $text;

    /**
     * @var Comments
     *
     * @ORM\ManyToOne(targetEntity="Comments", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reply_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Annotation\Type("AppBundle\Entity\Comments")
     * @Annotation\Groups({
     *     "get_comment", "get_comments", "post_comment", "put_comment"
     * })
     * @Annotation\SerializedName("reply_comments")
     */
    private $reply;

    /**
     * @var ArrayCollection|Comments[]
     *
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="reply", cascade={"persist"})
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     * @Annotation\Groups({
     *     "get_comment", "get_comments"
     * })
     */
    private $children;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set text.
     *
     * @param string $text
     *
     * @return Comments
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
     * @return Comments
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
     * @return Comments
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
     * Set reply.
     *
     * @param \AppBundle\Entity\Comments $reply
     *
     * @return Comments
     */
    public function setReply(\AppBundle\Entity\Comments $reply = null)
    {
        $this->reply = $reply;

        return $this;
    }

    /**
     * Get reply.
     *
     * @return \AppBundle\Entity\Comments
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Add child.
     *
     * @param \AppBundle\Entity\Comments $child
     *
     * @return Comments
     */
    public function addChild(\AppBundle\Entity\Comments $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child.
     *
     * @param \AppBundle\Entity\Comments $child
     */
    public function removeChild(\AppBundle\Entity\Comments $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @Annotation\VirtualProperty
     * @Annotation\Type("array")
     * @Annotation\SerializedName("author")
     * @Annotation\Groups({"get_comment", "get_comments"})
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

    /**
     * Set admin.
     *
     * @param \AppBundle\Entity\Admin $admin
     *
     * @return Comments
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
}
