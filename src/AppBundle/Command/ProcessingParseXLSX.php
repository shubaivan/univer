<?php

namespace AppBundle\Command;

use AppBundle\Entity\Courses;
use AppBundle\Entity\CoursesOfStudy;
use AppBundle\Entity\Lectors;
use AppBundle\Entity\SubCourses;
use AppBundle\Exception\ValidatorException;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\LockHandler;

class ProcessingParseXLSX extends ContainerAwareCommand
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
        $file_name = 'courses subtopics lecturers.xlsx';
        $excel = $this->getContainer()->get('phpexcel')
            ->createPHPExcelObject($dir.DIRECTORY_SEPARATOR.$file_name);
        $sheet = $excel->getActiveSheet();
        $app = $this->getContainer()->get('app.auth');
        $row = 2;
        $courseOfStudy = [];
        while ($sheet->getCellByColumnAndRow($row, 2)->getValue()) {
            $data = $sheet->getCellByColumnAndRow($row, 2)->getValue(); // get value from nth line and 2nf column
            $courseOfStudy[$row]['name'] = $data;
            ++$row;
        }
        $this->output->writeln('<comment>courseOfStudy finished</comment>');
        $mappingCourseOfStudy = [];
        $repo = $this->em
            ->getRepository('AppBundle:CoursesOfStudy');
        foreach ($courseOfStudy as $key => $value) {
            try {
                $entity = $app
                    ->processEntity(
                        CoursesOfStudy::getPostGrop(),
                        $value,
                        CoursesOfStudy::class
                    );
            } catch (ValidatorException $exception) {
                $entity = $repo
                    ->findOneBy(['name' => $value['name']]);
            }
            $this->output->writeln('<comment>courseOfStudy</comment>'.$key);
            $mappingCourseOfStudy[$key] = $entity;
            $this->em->persist($entity);
        }

        $this->em->flush();
        $this->output->writeln('<comment>courseOfStudy flush finished</comment>');

        $column = 3;
        $courses = [];
        while ($sheet->getCellByColumnAndRow(0, $column)->getValue()) {
            $data = $sheet->getCellByColumnAndRow(0, $column)->getValue();
            $dataId = $sheet->getCellByColumnAndRow(1, $column)->getValue();
            $courses[$dataId]['name'] = $data;

            for ($row = 2; $row <= 4; ++$row) {
                $subCoursesRelation = $sheet->getCellByColumnAndRow($row, $column)->getValue();
                if ($subCoursesRelation) {
                    $courses[$dataId]['courses_of_study'][$row] = $mappingCourseOfStudy[$row];
                }
            }

            ++$column;
        }
        $flushCourses = [];
        foreach ($courses as $key => $value) {
            try {
                $entity = $app
                    ->processEntity(
                        Courses::getPostGroup(),
                        $value,
                        Courses::class
                    );
            } catch (ValidatorException $exception) {
                $entity = $this->em
                    ->getRepository('AppBundle:Courses')
                    ->findOneBy(['name' => $value['name']]);
            }
            $this->em->persist($entity);
            $this->output->writeln('<comment>courses</comment>'.$key);
            $flushCourses[$key] = $entity;
        }

        $this->em->flush();
        $this->output->writeln('<comment>courseOfStudy flush finished</comment>');

        $sheet = $excel->getSheet(1);

        $column = 3;
        $subCourses = [];
        while ($sheet->getCellByColumnAndRow(0, $column)->getValue()) {
            $data = $sheet->getCellByColumnAndRow(0, $column)->getValue();
            $subCourses[$column]['name'] = $data;

            $courseId = $sheet->getCellByColumnAndRow(2, $column)->getValue();
            if ($courseId) {
                $subCourses[$column]['courses'][$column] = $flushCourses[$courseId];
            }
            ++$column;
        }

        $flushSubCourses = [];
        foreach ($subCourses as $key => $subCourse) {
            try {
                $entity = $app
                    ->processEntity(
                        SubCourses::getPostGroup(),
                        $subCourse,
                        SubCourses::class
                    );
            } catch (ValidatorException $exception) {
                $entity = $this->em
                    ->getRepository('AppBundle:SubCourses')
                    ->findOneBy(['name' => $subCourse['name']]);
            }
            $this->em->persist($entity);
            $this->output->writeln('<comment>SubCourses</comment>'.$key);
            $flushSubCourses[$key] = $entity;
        }

        $this->em->flush();
        $this->output->writeln('<comment>subCourses flush finished</comment>');

        $sheet = $excel->getSheet(2);

        $column = 3;
        $lectors = [];
        while ($sheet->getCellByColumnAndRow(0, $column)->getValue()) {
            $data = $sheet->getCellByColumnAndRow(0, $column)->getValue();
            $lectors[$column]['first_name'] = $data;

            ++$column;
        }

        $flushLectors = [];
        foreach ($lectors as $key => $lector) {
            try {
                $entity = $app
                    ->processEntity(
                        Lectors::getPostGroup(),
                        $lector,
                        Lectors::class
                    );
            } catch (ValidatorException $exception) {
                $entity = $this->em
                    ->getRepository('AppBundle:Lectors')
                    ->findOneBy(['firstName' => $lector['first_name']]);
            }
            $this->em->persist($entity);
            $this->output->writeln('<comment>Lector</comment>'.$key);
            $flushLectors[$key] = $entity;
        }

        $this->em->flush();
        $this->output->writeln('<comment>Lectors flush finished</comment>');
    }

    protected function configure()
    {
        $this
            ->setName('xlsx:parse')
            ->setDescription('xlsx parse');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);
        $lockHandler = new LockHandler('ProcessingVotes.lock');
        if (!$lockHandler->lock()) {
            $io->warning('Command ProcessingVotes already locked (previous command in progress)');

            return;
        }
        // @var EntityManager $em
        $this->em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        try {
            $this->readerAction();
        } catch (\Exception $e) {
            $io->warning($e->getMessage());
        }

        $io->success('ProcessingVotes successful');
        $lockHandler->release();
    }
}
