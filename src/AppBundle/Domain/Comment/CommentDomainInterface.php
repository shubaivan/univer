<?php

namespace AppBundle\Domain\Comment;

use AppBundle\Entity\Comments;

interface CommentDomainInterface
{
    /**
     * @param Comments $comment
     *
     * @return bool
     */
    public function approveComment(Comments $comment);
}
