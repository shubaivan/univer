<?php

namespace AppBundle\Domain\Comment;

use AppBundle\Application\Notifications\NotificationsApplication;
use AppBundle\Entity\Admin;
use AppBundle\Entity\Comments;
use AppBundle\Entity\Enum\ProviderTypeEnum;
use AppBundle\Entity\Questions;
use AppBundle\Entity\Repository\CommentsRepository;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CommentDomain implements CommentDomainInterface
{
    /**
     * @var CommentsRepository
     */
    private $commentsRepository;

    /**
     * @var NotificationsApplication
     */
    private $notificationsApplication;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * CommentDomain constructor.
     *
     * @param CommentsRepository       $commentsRepository
     * @param NotificationsApplication $notificationsApplication
     */
    public function __construct(
        CommentsRepository $commentsRepository,
        NotificationsApplication $notificationsApplication,
        TokenStorage $tokenStorage
    ) {
        $this->commentsRepository = $commentsRepository;
        $this->notificationsApplication = $notificationsApplication;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function approveComment(Comments $comment)
    {
        if (!$comment->getApprove()
            || !$this->tokenStorage->getToken()
            || !$this->tokenStorage->getToken()->getUser() instanceof Admin) {
            return false;
        }
        /** @var Questions $question */
        $question = $comment->getQuestions();
        /** @var Comments[] $comments */
        $comments = $this->getCommentsRepository()
            ->findBy(['questions' => $question]);
        $userBuffer = new ArrayCollection();
        foreach ($comments as $value) {
            if ($value->getUser() instanceof User
                && !$userBuffer->contains($value->getUser())) {
                $userBuffer->add($value->getUser());
                $this->notificationsApplication
                    ->createNotification(
                        $value->getUser(),
                        $comment->getUser(),
                        ProviderTypeEnum::TYPE_PROVIDER_QUESTIONS,
                        $question->getId(),
                        'comments has been updated'
                    );
            }
        }

        return true;
    }

    /**
     * @return CommentsRepository
     */
    private function getCommentsRepository()
    {
        return $this->commentsRepository;
    }
}
