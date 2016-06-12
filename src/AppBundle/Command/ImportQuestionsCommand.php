<?php

namespace AppBundle\Command;

use AppBundle\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportQuestionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:questions')
            ->setDescription('Import questions from csv')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'CSV File'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $fileContent = trim(file_get_contents($file));
        $fileLines = explode("\n", $fileContent);
        $nbLines = count($fileLines);

        $progress = new ProgressBar($output, $nbLines);

        $progress->start();

        for ($i = 0; $i < $nbLines; $i++) {

            $line = explode(';', $fileLines[$i]);

            $type = trim($line[0]);
            $question = trim($line[1]);

            $q = new Question();
            $q->setQuestion($question);
            if ($type === 'multi') {
                $q->setIsGroup(true);
            }

            $this->getContainer()->get('doctrine.orm.entity_manager')->persist($q);

            $progress->advance();
        }

        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();

        $progress->finish();

        $output->writeln("");
    }
}
