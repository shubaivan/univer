<?php

namespace AppBundle\Entity\Collections;

interface AbstractCollectionsInterface
{
    /**
     * @return array
     */
    public function getCollection();

    /**
     * @return array
     */
    public function getTotal();
}
