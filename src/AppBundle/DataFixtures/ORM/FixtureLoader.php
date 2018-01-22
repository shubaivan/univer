<?php

namespace AppBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Security\Core\User\UserInterface;

class FixtureLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            __DIR__.'/../Fixtures/admin.yml',
        ];
    }

    /**
     * @param UserInterface $user
     * @param $plainPassword
     *
     * @return string
     */
    public function encodePassword(UserInterface $user, $plainPassword)
    {
        return $this->container->get('security.password_encoder')->encodePassword($user, $plainPassword);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setValue($value)
    {
        return $value;
    }
}
