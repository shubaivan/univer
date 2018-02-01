<?php

namespace AppBundle\Domain\SubCourses;

use AppBundle\Entity\Repository\NotesRepository;
use AppBundle\Entity\Repository\SubCoursesRepository;
use FOS\RestBundle\Request\ParamFetcher;

class SubCoursesDomain implements SubCoursesDomainInterface
{
    /**
     * @var SubCoursesRepository
     */
    private $subCoursesRepository;

    /**
     * @var NotesRepository
     */
    private $notesRepository;

    /**
     * SubCoursesDomain constructor.
     *
     * @param SubCoursesRepository $subCoursesRepository
     */
    public function __construct(
        SubCoursesRepository $subCoursesRepository,
        NotesRepository $notesRepository
    ) {
        $this->subCoursesRepository = $subCoursesRepository;
        $this->notesRepository = $notesRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubCourses(ParamFetcher $paramFetcher)
    {
        $subCourses = $this->getSubCoursesRepository()
            ->getEntitiesByParams($paramFetcher);

        foreach ($subCourses as $key => $subCours) {
            $subCourses[$subCours['sub_courses_name']] = $subCourses[$key];
            $ids = explode(',', $subCours['note_ids']);
            $notes = $this->getNotesRepository()
                    ->getEntitiesByIds($ids);
            $subCourses[$subCours['sub_courses_name']]['notes'] = $notes;
            unset($subCourses[$key]);
        }

        return $subCourses;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubCoursesCount(ParamFetcher $paramFetcher)
    {
        return $this->getSubCoursesRepository()
            ->getEntitiesByParams($paramFetcher, true);
    }

    /**
     * @return SubCoursesRepository
     */
    private function getSubCoursesRepository()
    {
        return $this->subCoursesRepository;
    }

    /**
     * @return NotesRepository
     */
    private function getNotesRepository()
    {
        return $this->notesRepository;
    }
}
