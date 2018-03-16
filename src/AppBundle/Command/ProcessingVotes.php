<?php

namespace AppBundle\Command;

use AppBundle\Entity\Questions;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\LockHandler;

class ProcessingVotes extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('votes:processing')
            ->setDescription('votes processing');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $lockHandler = new LockHandler('ProcessingVotes.lock');
        if (!$lockHandler->lock()) {
            $io->warning('Command ProcessingVotes already locked (previous command in progress)');

            return;
        }
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        try {
            /** @var Questions[] $questions */
            $questions = $this->getContainer()->get('app.repository.questions')
                ->getEntitiesForProcessing();

            foreach ($questions as $question) {
                $votes = $this->getContainer()->get('app.repository.votes')
                    ->findBy(['questions' => $question]);
                foreach ($votes as $vote) {
                    $em->remove($vote);
                }
            }

            $em->flush();
        } catch (\Exception $e) {
            $io->warning($e->getMessage());
        }

        $io->success('ProcessingVotes successful');
        $lockHandler->release();
    }
}
