<?php

namespace AppBundle\Application\SubCourses;

use AppBundle\Domain\SubCourses\SubCoursesDomainInterface;
use AppBundle\Entity\Collections\SubCourses\SubCoursesCollection;
use FOS\RestBundle\Request\ParamFetcher;

class SubCoursesApplication implements SubCoursesApplicationInterface
{
    /**
     * @var SubCoursesDomainInterface
     */
    private $subCoursesDomain;

    /**
     * EbooksApplication constructor.
     *
     * @param SubCoursesDomainInterface $subCoursesDomain
     */
    public function __construct(
        SubCoursesDomainInterface $subCoursesDomain
    ) {
        $this->subCoursesDomain = $subCoursesDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubCoursesCollection(ParamFetcher $paramFetcher)
    {
        $subCourses = $this->getSubCoursesDomainInterface()
            ->getSubCourses($paramFetcher);
        $total = $this->getSubCoursesDomainInterface()
            ->getSubCoursesCount($paramFetcher);

        return new SubCoursesCollection($subCourses, $total);
    }

    /**
     * @return SubCoursesDomainInterface
     */
    private function getSubCoursesDomainInterface()
    {
        return $this->subCoursesDomain;
    }
}
