<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Questions;

use AppBundle\Entity\Repository\NotesRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Add data after serialization
 */
class SerializationListener implements EventSubscriberInterface
{
    private $tokenStorage;

    private $user;

    private $notesRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        NotesRepository $notesRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->notesRepository = $notesRepository;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @inheritdoc
     */
    static public function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'class' => Questions::class,
                'method' => 'onPostSerialize'
            ),
            array(
                'event' => 'serializer.pre_serialize',
                'class' => Questions::class,
                'method' => 'onPreSerialize'
            ),
        );
    }

    public function onPostSerialize(ObjectEvent $event)
    {

    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        /** @var Questions $question */
        $question = $event->getObject();
        if ($this->user instanceof User) {
            $authorNotes = $this->notesRepository->findBy(['user' => $this->user, 'questions' => $question]);
            $question->setNoteCollection($authorNotes);
        }
    }
}