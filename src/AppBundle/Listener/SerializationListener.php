<?php

namespace AppBundle\Listener;

use AppBundle\Entity\AbstractUser;
use AppBundle\Entity\Questions;
use AppBundle\Entity\Repository\FavoritesRepository;
use AppBundle\Entity\Repository\NotesRepository;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Add data after serialization.
 */
class SerializationListener implements EventSubscriberInterface
{
    const FAVORITES_COUNT = 'count';
    const FAVORITES_AUTH_MARK = 'auth_mark';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var User
     */
    private $user;

    /**
     * @var NotesRepository
     */
    private $notesRepository;

    /**
     * @var FavoritesRepository
     */
    private $favoritesRepository;

    /**
     * @var array
     */
    private $favoriteObject;

    /**
     * SerializationListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param NotesRepository       $notesRepository
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        NotesRepository $notesRepository,
        FavoritesRepository $favoritesRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->notesRepository = $notesRepository;
        $this->favoritesRepository = $favoritesRepository;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->favoriteObject = [
            self::FAVORITES_COUNT => 0,
            self::FAVORITES_AUTH_MARK => false
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => Questions::class,
                'method' => 'onPreSerialize',
            ],
            [
                'event' => 'serializer.post_serialize',
                'class' => Questions::class,
                'method' => 'onPreSerializeQuestions',
            ],
            [
                'event' => 'serializer.post_deserialize',
                'class' => User::class,
                'method' => 'onPostDeserialize',
            ],
        ];
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        /** @var User $user */
        $user = $event->getObject();

        $user->getUserRoles()->filter(function ($entry) {
            /** @var Role $entry */
            if (AbstractUser::ROLE_ADMIN === $entry->getName()) {
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

        $authorFavorites = $this->favoritesRepository
            ->findOneBy(['user' => $this->user, 'questions' => $question]);
        $this->favoriteObject[self::FAVORITES_COUNT] = $question->getFavorites()->count();
        if ($authorFavorites) {
            $this->favoriteObject[self::FAVORITES_AUTH_MARK] = true;
        } else {
            $this->favoriteObject[self::FAVORITES_AUTH_MARK] = false;
        }
    }

    public function onPreSerializeQuestions(ObjectEvent $event)
    {
        $event->getVisitor()->addData('favorites', $this->favoriteObject);
    }
}
