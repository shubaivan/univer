<?php

namespace AppBundle\Services;

use AppBundle\Controller\Api\AbstractRestController;
use AppBundle\Entity\Enum\QuestionsTypeEnum;
use AppBundle\Entity\Questions;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * ImportManager constructor.
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ContainerInterface $container,
        EntityManager $entityManager,
        ObjectManager $objectManager
    ) {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->objectManager = $objectManager;
    }

    /**
     * @param $path
     */
    public function importQuestions($path, $output = null)
    {
        $console = false;
        if ($output instanceof OutputInterface) {
            $this->setOutputInterface($output);
            $console = true;
        }
        $excel = $this->getContainer()->get('phpexcel')
            ->createPHPExcelObject($path);
        $sheet = $excel->getActiveSheet();
        $app = $this->getObjectManager();
        $courseOfStudyRepo = $this->getEntityManager()->getRepository('AppBundle:CoursesOfStudy');
        $coursesRepo = $this->getEntityManager()->getRepository('AppBundle:Courses');
        $subCoursesRepo = $this->getEntityManager()->getRepository('AppBundle:SubCourses');
        $semestersRepo = $this->getEntityManager()->getRepository('AppBundle:Semesters');
        $examPeriodsRepo = $this->getEntityManager()->getRepository('AppBundle:ExamPeriods');
        $lectorsRepo = $this->getEntityManager()->getRepository('AppBundle:Lectors');
        $userRepo = $this->getEntityManager()->getRepository('AppBundle:User');
        $questions = [];
        $row = 4;
        while ($sheet->getCellByColumnAndRow(0, $row)->getValue()) {
            $data = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            !$console ? : $this->getOutputInterface()->writeln('<comment>parse start</comment>'.$row);
            if ($data) {
                for ($i = 0; $i <= 29; ++$i) {
                    $data = $sheet->getCellByColumnAndRow($i, $row)->getValue();
                    if ($data && 0 === $i) {
                        $names = explode(',', $data);
                        if ($names[0]) {
                            $courseOfStudy = $courseOfStudyRepo->findOneBy(['name' => $names[0]]);
                            if ($courseOfStudy) {
                                $questions[$row]['courses_of_study'] = $courseOfStudy->getId();
                            }
                        }
                    }
                    if ($data && 1 === $i) {
                        if ($coursesRepo->findOneBy(['courseNum' => $data])) {
                            $questions[$row]['courses'] = $coursesRepo->findOneBy(['courseNum' => $data])->getId();
                        }
                    }

                    if (2 === $i) {
                        $questions[$row]['sub_courses'] = $subCoursesRepo
                            ->findAll()[array_rand($subCoursesRepo->findAll())]->getId();
                    }

                    if ($data && 3 === $i) {
                        $questions[$row]['year'] = $data;
                    }

                    if ($data && 4 === $i) {
                        $semester = $semestersRepo->findOneBy(['name' => $data]);
                        if ($semester) {
                            $questions[$row]['semesters'] = ['id' => $semester->getId()];
                        } else {
                            $questions[$row]['semesters'] = ['name' => $data];
                        }
                    }

                    if ($data && 5 === $i) {
                        $examPeriod = $examPeriodsRepo->findOneBy(['name' => $data]);
                        if ($examPeriod) {
                            $questions[$row]['exam_periods'] = ['id' => $examPeriod->getId()];
                        } else {
                            $questions[$row]['exam_periods'] = ['name' => $data];
                        }
                    }

                    if (6 === $i) {
                        $questions[$row]['lectors'] = $lectorsRepo
                            ->findAll()[array_rand($lectorsRepo->findAll())]->getId();
                    }

                    if ($data && 7 === $i) {
                        $questions[$row]['question_number'] = $data;
                    }

                    if ($data && 8 === $i) {
                        $valueType = array_search($data, QuestionsTypeEnum::getAvailableTypes());
                        $questions[$row]['type'] = $valueType;
                    }

                    if ($data && 13 === $i) {
                        $questions[$row]['text'] = $data;
                    }

                    if ($data && 15 === $i) {
                        $questions[$row]['notes'] = $data;
                    }

                    if ($data && 19 === $i) {
                        $questions[$row]['question_answers'][0]['answer'] = $data;
                    }

                    if (20 === $i) {
                        $questions[$row]['question_answers'][0]['is_true'] = $data;
                    }

                    if ($data && 22 === $i) {
                        $questions[$row]['question_answers'][1]['answer'] = $data;
                    }

                    if (23 === $i) {
                        $questions[$row]['question_answers'][1]['is_true'] = $data;
                    }

                    if ($data && 25 === $i) {
                        $questions[$row]['question_answers'][2]['answer'] = $data;
                    }

                    if (26 === $i) {
                        $questions[$row]['question_answers'][2]['is_true'] = $data;
                    }

                    if ($data && 28 === $i) {
                        $questions[$row]['question_answers'][3]['answer'] = $data;
                    }

                    if (29 === $i) {
                        $questions[$row]['question_answers'][3]['is_true'] = $data;
                    }

                    $questions[$row]['user'] = $userRepo->findOneBy(['id' => 2])->getId();
                }
            }
            ++$row;
        }
        !$console ? : $this->getOutputInterface()->writeln('<comment>persist start</comment>');
        foreach ($questions as $key => $question) {
            $entity = $app->processEntity(
                Questions::getImportGroup(),
                $question,
                Questions::class
            );
            $this->getEntityManager()->persist($entity);
            !$console ? : $this->getOutputInterface()->writeln('<comment>persist</comment>'.$key);
        }
        !$console ? : $this->getOutputInterface()->writeln('<comment>flush start</comment>');
        $this->getEntityManager()->flush();
        !$console ? : $this->getOutputInterface()->writeln('<comment>flush finish</comment>');
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return $this->container;
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return ObjectManager
     */
    private function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return OutputInterface
     */
    private function getOutputInterface()
    {
        return $this->output;
    }

    /**
     * @return OutputInterface
     */
    private function setOutputInterface($output)
    {
        return $this->output = $output;
    }
}
