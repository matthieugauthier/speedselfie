<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:users')
            ->setDescription('Import users from csv')
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

            $gaia = trim($line[2]);
            $firstName = trim($line[0]);
            $lastName = trim($line[1]);
            $plainPassword = trim($line[3]);

            $u = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('AppBundle:User')->findByUsername($gaia);

            if (!$u) {
                $u = new User();

                $encoder = $this->getContainer()->get('security.password_encoder');
                $encoded = $encoder->encodePassword($u, $plainPassword);

                $u->setUsername($gaia)
                    ->setPassword($encoded)
                    ->setFirstName($firstName)
                    ->setLastName($lastName);

                $this->getContainer()->get('doctrine.orm.entity_manager')->persist($u);
            }

            $progress->advance();
        }

        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();

        $progress->finish();

        $output->writeln("");
    }
}