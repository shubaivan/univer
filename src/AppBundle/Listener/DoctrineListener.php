<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Questions;
use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
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
        ];
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
