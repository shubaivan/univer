<?php

namespace AppBundle\Command;

use AppBundle\Entity\Courses;
use AppBundle\Entity\CoursesOfStudy;
use AppBundle\Entity\Enum\QuestionsTypeEnum;
use AppBundle\Entity\Lectors;
use AppBundle\Entity\Questions;
use AppBundle\Entity\SubCourses;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\LockHandler;

class ProcessingParseQuestionXLSX extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var OutputInterface
     */
    private $output;

    public function readerAction()
    {
        $dir = $this->getContainer()->getParameter('kernel.root_dir').'/../bin/data/import/';
        $file_name = 'questions for course 1023108.xlsx';
        $excel = $this->getContainer()->get('phpexcel')
            ->createPHPExcelObject($dir.DIRECTORY_SEPARATOR.$file_name);
        $sheet = $excel->getActiveSheet();
        $app = $this->getContainer()->get('app.auth');
        $courseOfStudyRepo = $this->em->getRepository('AppBundle:CoursesOfStudy');
        $coursesRepo = $this->em->getRepository('AppBundle:Courses');
        $subCoursesRepo = $this->em->getRepository('AppBundle:SubCourses');
        $semestersRepo = $this->em->getRepository('AppBundle:Semesters');
        $examPeriodsRepo = $this->em->getRepository('AppBundle:ExamPeriods');
        $lectorsRepo = $this->em->getRepository('AppBundle:Lectors');
        $userRepo = $this->em->getRepository('AppBundle:User');
        $questions = [];
        $row = 4;
        while ($sheet->getCellByColumnAndRow(0, $row)->getValue()) {
            $data = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $this->output->writeln('<comment>parse start</comment>'.$row);
            if ($data) {
                for ($i = 0; $i <= 29; ++$i) {
                    $data = $sheet->getCellByColumnAndRow($i, 4)->getValue();
                    if ($data && 0 === $i) {
                        $names = explode(',', $data);
                        $courseOfStudy = $courseOfStudyRepo->findOneBy(['name' => $names[0]]);
                        $questions[$row]['courses_of_study'] = $courseOfStudy->getId();
                    }
                    if ($data && 1 === $i) {
                        $questions[$row]['courses'] = $coursesRepo->findOneBy(['courseNum' => $data])->getId();
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
                        $valueType = array_search($data, QuestionsTypeEnum::getAvailableTypes(), true);
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
        $this->output->writeln('<comment>persist start</comment>');
        foreach ($questions as $key => $question) {
            $entity = $app->processEntity(
                Questions::getPostGroup(),
                $question,
                Questions::class
            );
            $this->em->persist($entity);
            $this->output->writeln('<comment>persist</comment>'.$key);
        }
        $this->output->writeln('<comment>flush start</comment>');
        $this->em->flush();
        $this->output->writeln('<comment>flush finish</comment>');
    }

    protected function configure()
    {
        $this
            ->setName('xlsx:question:parse')
            ->setDescription('xlsx question parse');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);
        $lockHandler = new LockHandler('ProcessingParseQuestionXLSX.lock');
        if (!$lockHandler->lock()) {
            $io->warning('Command ProcessingParseQuestionXLSX already locked (previous command in progress)');

            return;
        }
        // @var EntityManager $em
        $this->em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        try {
            $this->readerAction();
        } catch (\Exception $e) {
            $io->warning($e->getMessage());
        }

        $io->success('ProcessingParseQuestionXLSX successful');
        $lockHandler->release();
    }
}
