<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\Application\PassportRegisterUpdater;

class PassportUpdateCommand extends Command
{
    protected static $defaultName = 'app:passport-register-update';

    private $passportRegisterUpdater;

    public function __construct(PassportRegisterUpdater $passportRegisterUpdater)
    {
        parent::__construct();
        $this->passportRegisterUpdater = $passportRegisterUpdater;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates latest passport register data (if modified)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $output->writeln('Updating passport register...');
        $result = $this->passportRegisterUpdater->updatePassportRegister();

        if ($result) {
            $io->success('Passport register was successfully updated.'); //date(DATE_RFC822)
            return Command::SUCCESS;
        } else {
            $io->warning('Passport register was not updated. (Check logs)');
            return Command::FAILURE;
        }
    }
}
