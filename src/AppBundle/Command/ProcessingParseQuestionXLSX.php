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

        $path = $dir.DIRECTORY_SEPARATOR.$file_name;

        $this->getContainer()->get('app.service.import_manager')
            ->importQuestions($path, $this->output);
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
