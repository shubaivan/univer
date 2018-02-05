<?php

namespace AppBundle\Domain\SubCourses;

use AppBundle\Entity\Repository\NotesRepository;
use AppBundle\Entity\Repository\QuestionsRepository;
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
     * @var QuestionsRepository
     */
    private $questionsRepository;

    /**
     * SubCoursesDomain constructor.
     *
     * @param SubCoursesRepository $subCoursesRepository
     * @param NotesRepository      $notesRepository
     * @param QuestionsRepository  $questionsRepository
     */
    public function __construct(
        SubCoursesRepository $subCoursesRepository,
        NotesRepository $notesRepository,
        QuestionsRepository $questionsRepository
    ) {
        $this->subCoursesRepository = $subCoursesRepository;
        $this->notesRepository = $notesRepository;
        $this->questionsRepository = $questionsRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubCourses(ParamFetcher $paramFetcher)
    {
        $subCourses = $this->getSubCoursesRepository()
            ->getEntitiesByParamsRelation($paramFetcher);

        foreach ($subCourses as $key => $subCours) {
            $ids = [];
            if ($subCours['question_ids']) {
                $ids = array_unique(explode(',', $subCours['question_ids']));
            }
            $questions = $this->getQuestionsRepository()
                    ->getEntitiesByIds($ids);

            foreach ($questions as $keyQuestion => $question) {
                $noteIds = [];
                if ($question['note_ids']) {
                    $noteIds = array_unique(explode(',', $question['note_ids']));
                }
                $params = $paramFetcher->getParams();
                $author = null;
                if (array_key_exists('user', $params) && $paramFetcher->get('user')) {
                    $author = $paramFetcher->get('user');
                }
                $notes = $this->getNotesRepository()
                    ->getEntitiesByIds($noteIds, $author);
                if (!$notes) {
                    unset($questions[$keyQuestion]);
                    continue;
                }
                $questions[$keyQuestion]['notes'] = $notes;
                unset($questions[$keyQuestion]['note_ids']);
            }
            if (!$questions) {
                unset($subCourses[$key]);
                continue;
            }

            $subCourses[$subCours['sub_courses_name']] = $subCourses[$key];
            $subCourses[$subCours['sub_courses_name']]['questions'] = $questions;
            unset($subCourses[$subCours['sub_courses_name']]['question_ids'], $subCourses[$key]);
        }

        return $subCourses;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubCoursesCount(ParamFetcher $paramFetcher)
    {
        return $this->getSubCoursesRepository()
            ->getEntitiesByParamsRelation($paramFetcher, true);
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

    /**
     * @return QuestionsRepository
     */
    private function getQuestionsRepository()
    {
        return $this->questionsRepository;
    }
}
