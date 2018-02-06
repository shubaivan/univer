<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Questions;
use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

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
        return array(
            'preRemove'
        );
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
