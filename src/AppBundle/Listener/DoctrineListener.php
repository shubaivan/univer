<?php

namespace AppBundle\Listener;

use AppBundle\Application\Notifications\NotificationsApplication;
use AppBundle\Entity\Enum\ProviderTypeEnum;
use AppBundle\Entity\QuestionAnswers;
use AppBundle\Entity\QuestionCorrections;
use AppBundle\Entity\Questions;
use AppBundle\Entity\User;
use AppBundle\Entity\UserQuestionAnswerTest;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineListener implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'onFlush',
            'postFlush'
        ];
    }
    public function postFlush(PostFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $em->getFilters()->enable('softdeleteable');
        $identityMap = $uow->getIdentityMap();
        foreach ($identityMap as $key=>$entity) {
            if ($key === Questions::class) {
                $repo = $this->container->get('app.repository.questions');
                /** @var Questions $value */
                foreach ($entity as $value) {
                    $data = $value->generateNumber();
                    $repo->updateNumber($data, $value->getId());
                    if (!$value->getCustomId()) {
                        $repo->updateCustomerId($value->getId());
                    }
                }
            }
        }
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $em->getFilters()->enable('softdeleteable');

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof QuestionCorrections) {
                $answers = $entity->getQuestionAnswers();
                /** @var QuestionAnswers[] $existAnswers */
                $existAnswers = $this->container->get('app.repository.question_answers')
                    ->findBy(['questionCorrections' => $entity]);
                if (array_diff($existAnswers, $answers->toArray())) {
                    foreach ($existAnswers as $existAnswer) {
                        if (!$answers->contains($existAnswer)) {
                            $em->remove($existAnswer);
                        }
                    }
                }
            }

            if ($entity instanceof Questions) {
                $entity->setQuestionNumber();
                $answers = $entity->getQuestionAnswers();
                $existAnswers = $this->container->get('app.repository.question_answers')
                    ->findBy(['questions' => $entity]);
                if (array_diff($answers->toArray(), $existAnswers)) {
                    /** @var NotificationsApplication $notificationApplication */
                    $notificationApplication = $this->container
                        ->get('app.application.notifications_application');

                    foreach ($existAnswers as $existAnswer) {
                        if (!$answers->contains($existAnswer)) {
                            /** @var UserQuestionAnswerTest[] $userAnswersTest */
                            $userAnswersTest = $existAnswer->getUserQuestionAnswerTest();
                            foreach ($userAnswersTest as $value) {
                                $notificationApplication->createNotification(
                                    $value->getUser(),
                                    $entity->getUser(),
                                    ProviderTypeEnum::TYPE_PROVIDER_QUESTION_ANSWER_TEST,
                                    $value->getId(),
                                    'update your answers',
                                    true
                                );
                            }
                            $em->remove($existAnswer);
                        }
                    }
                }
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Questions) {
            $favorites = $this->container->get('doctrine.orm.default_entity_manager')
                ->getRepository('AppBundle:Favorites')->findBy(['questions' => $entity->getId()]);
            foreach ($favorites as $value) {
                $this->container->get('doctrine.orm.default_entity_manager')->remove($value);
            }
        }

        if ($entity instanceof User) {
            $favorites = $this->container->get('doctrine.orm.default_entity_manager')
                ->getRepository('AppBundle:Favorites')->findBy(['user' => $entity->getId()]);
            foreach ($favorites as $value) {
                $this->container->get('doctrine.orm.default_entity_manager')->remove($value);
            }
        }
    }
}
