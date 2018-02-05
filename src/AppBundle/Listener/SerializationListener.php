<?php

namespace AppBundle\Listener;

use AppBundle\Entity\AbstractUser;
use AppBundle\Entity\Questions;

use AppBundle\Entity\Repository\NotesRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\Validator\Exception\ValidatorException;


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
                'event' => 'serializer.pre_serialize',
                'class' => Questions::class,
                'method' => 'onPreSerialize'
            ),
            array(
                'event' => 'serializer.post_deserialize',
                'class' => User::class,
                'method' => 'onPostDeserialize'
            ),
        );
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        /** @var User $user */
        $user = $event->getObject();

        $user->getUserRoles()->filter(function ($entry) {
            if ($entry->getName() == AbstractUser::ROLE_ADMIN) {
                throw new ValidatorException('forbidden role for user');
            }
        });
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