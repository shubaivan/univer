<?php

namespace AppBundle\Application\SubCourses;

use AppBundle\Entity\Collections\Ebooks\EbooksCollection;
use FOS\RestBundle\Request\ParamFetcher;

interface SubCoursesApplicationInterface
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return EbooksCollection
     */
    public function getSubCoursesCollection(ParamFetcher $paramFetcher);
}
