<?php

namespace AppBundle\Entity\Interfaces;

interface NotificationInterface
{
    /**
     * @return array
     */
    public static function getPostGroup();

    /**
     * @return array
     */
    public static function getGetGroup();
}
