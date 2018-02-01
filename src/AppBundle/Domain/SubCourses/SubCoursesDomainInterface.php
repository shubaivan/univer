<?php

namespace AppBundle\Domain\SubCourses;

use FOS\RestBundle\Request\ParamFetcher;

interface SubCoursesDomainInterface
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function getSubCourses(ParamFetcher $paramFetcher);

    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return int
     */
    public function getSubCoursesCount(ParamFetcher $paramFetcher);
}
