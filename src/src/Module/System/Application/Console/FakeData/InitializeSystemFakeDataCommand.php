<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\FakeData;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:initialize-system-fake-data')]
class InitializeSystemFakeDataCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Executes all fake initialization commands: companies, departments, employees.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        if (!$application) {
            $output->writeln('<error>Application is not available</error>');

            return Command::FAILURE;
        }

        $commands = [
            'app:add-record-to-company-table',
            'app:add-record-to-department-table',
        ];

        $output->writeln("**************************************************************");
        foreach ($commands as $commandName) {
            $output->writeln("--------------------------------------------------------------");
            $output->writeln("<info>Command execution: $commandName</info>");
            $command = $application->find($commandName);
            $result = $command->run(new ArrayInput([]), $output);

            if ($result !== Command::SUCCESS) {
                $output->writeln("<error>Error during execution: $commandName</error>");

                return $result;
            }

            $output->writeln("--------------------------------------------------------------");
        }

        $output->writeln("");
        $output->writeln('<info>All commands were executed successfully :)</info>');
        $output->writeln("");
        $output->writeln("**************************************************************");

        return Command::SUCCESS;
    }
}