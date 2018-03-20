<?php

namespace AppBundle\Domain\Comment;

use AppBundle\Application\Notifications\NotificationsApplication;
use AppBundle\Entity\Admin;
use AppBundle\Entity\Comments;
use AppBundle\Entity\Enum\ProviderTypeEnum;
use AppBundle\Entity\Repository\CommentsRepository;
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
            || !$this->tokenStorage->getToken()->getUser() instanceof Admin) {
            return false;
        }
        $questions = $comment->getQuestions();
        /** @var Comments[] $comments */
        $comments = $this->getCommentsRepository()
            ->findBy(['questions' => $questions]);

        foreach ($comments as $value) {
            $this->notificationsApplication
                ->createNotification(
                    $value->getUser(),
                    $comment->getUser(),
                    ProviderTypeEnum::TYPE_PROVIDER_COMMENT,
                    'comments has been updated'
                );
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
