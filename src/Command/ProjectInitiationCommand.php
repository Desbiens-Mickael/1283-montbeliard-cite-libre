<?php

namespace App\Command;

use App\Service\StartupDataManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:project-initiation',
    description: 'Initializes the project by filling the tables with start-up data.',
)]
class ProjectInitiationCommand extends Command
{
    public function __construct(
        private StartupDataManager $startupDataManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Initialize startup data');

        $this->startupDataManager->generateDataFromArray();
        $this->startupDataManager->generatingDataFromCsvFile();

        $style->success('All tables were successfully filled!');

        return Command::SUCCESS;
    }
}
