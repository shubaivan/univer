<?php

namespace AppBundle\Listener;

use AppBundle\Entity\AbstractUser;
use AppBundle\Entity\Admin;
use AppBundle\Entity\Favorites;
use AppBundle\Entity\Questions;
use AppBundle\Entity\RepeatedQuestions;
use AppBundle\Entity\Repository\FavoritesRepository;
use AppBundle\Entity\Repository\NotesRepository;
use AppBundle\Entity\Repository\RepeatedQuestionsRepository;
use AppBundle\Entity\Repository\UserQuestionAnswerTestRepository;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\UserQuestionAnswerTest;
use AppBundle\Model\Request\UserQuestionAnswerTestRequestModel;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Add data after serialization.
 */
class SerializationListener implements EventSubscriberInterface
{
    const COUNT = 'count';
    const FAVORITES_AUTH_MARK = 'favorite_id';
    const REPEATED_QUESTION_AUTH_MARK = 'repeated_question_id';

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
     * @var array
     */
    private $repeatedQuestionObject;

    /**
     * @var UserQuestionAnswerTestRepository
     */
    private $userQuestionAnswerTestRepository;

    /**
     * @var RepeatedQuestionsRepository
     */
    private $repeatedQuestionsRepository;

    /**
     * SerializationListener constructor.
     *
     * @param TokenStorageInterface            $tokenStorage
     * @param NotesRepository                  $notesRepository
     * @param FavoritesRepository              $favoritesRepository
     * @param UserQuestionAnswerTestRepository $answerTestRepository
     * @param RepeatedQuestionsRepository      $repeatedQuestionsRepository
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        NotesRepository $notesRepository,
        FavoritesRepository $favoritesRepository,
        UserQuestionAnswerTestRepository $answerTestRepository,
        RepeatedQuestionsRepository $repeatedQuestionsRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->notesRepository = $notesRepository;
        $this->favoritesRepository = $favoritesRepository;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->favoriteObject = [
            self::COUNT => 0,
            self::FAVORITES_AUTH_MARK => null,
        ];

        $this->repeatedQuestionObject = [
            self::COUNT => 0,
            self::REPEATED_QUESTION_AUTH_MARK => null,
        ];

        $this->userQuestionAnswerTestRepository = $answerTestRepository;
        $this->repeatedQuestionsRepository = $repeatedQuestionsRepository;
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
            [
                'event' => 'serializer.pre_deserialize',
                'class' => UserQuestionAnswerTestRequestModel::class,
                'method' => 'onPreDeserializeUQATRM',
            ],
        ];
    }

    public function onPreDeserializeUQATRM(PreDeserializeEvent $event)
    {
        $models = $event->getData();

        if (!array_key_exists('answers', $models) || !is_array($models['answers'])) {
            return $models;
        }

        if ($this->user instanceof User) {
            $auth = ['user' => $this->user->getId()];
        } elseif ($this->user instanceof Admin) {
            $auth = ['admin' => $this->user->getId()];
        } else {
            throw new AccessDeniedException();
        }

        if ($this->user instanceof User) {
            foreach ($models['answers'] as $key => $model) {
                if (!is_array($model['question_answers']) || !array_key_exists('id', $model['question_answers'])) {
                    continue;
                }
                $id = [];
                /** @var UserQuestionAnswerTest $entity */
                $entity = $this->getUserQuestionAnswerTestRepository()
                    ->findOneBy(['user' => $this->user, 'questionAnswers' => $model['question_answers']['id']]);
                if ($entity) {
                    $id = ['id' => $entity->getId()];
                }
                $models['answers'][$key] = array_merge($models['answers'][$key], $auth, $id);
            }
        }

        $event->setData($models);
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
        /** @var Favorites $authorFavorites */
        $authorFavorites = $this->favoritesRepository
            ->findOneBy(['user' => $this->user, 'questions' => $question]);
        /** @var RepeatedQuestions $authorRepeatedQuestions */
        $authorRepeatedQuestions = $this->repeatedQuestionsRepository
            ->findOneBy(['user' => $this->user, 'questions' => $question]);
        $this->favoriteObject[self::COUNT] = $question->getFavorites()->count();
        $this->repeatedQuestionObject[self::COUNT] = ($question->getRepeatedQuestions() ? $question->getRepeatedQuestions()->count() : 0);
        if ($authorFavorites) {
            $this->favoriteObject[self::FAVORITES_AUTH_MARK] = $authorFavorites->getId();
        } else {
            $this->favoriteObject[self::FAVORITES_AUTH_MARK] = null;
        }

        if ($authorRepeatedQuestions) {
            $this->repeatedQuestionObject[self::REPEATED_QUESTION_AUTH_MARK] = $authorRepeatedQuestions->getId();
        } else {
            $this->repeatedQuestionObject[self::REPEATED_QUESTION_AUTH_MARK] = null;
        }
    }

    public function onPreSerializeQuestions(ObjectEvent $event)
    {
        $event->getVisitor()->addData('favorites', $this->favoriteObject);
        $event->getVisitor()->addData('repeated_questions', $this->repeatedQuestionObject);
    }

    /**
     * @return UserQuestionAnswerTestRepository
     */
    private function getUserQuestionAnswerTestRepository()
    {
        return $this->userQuestionAnswerTestRepository;
    }
}
