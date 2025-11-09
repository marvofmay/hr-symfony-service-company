<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:initialize-system-default-data')]
class InitializeSystemDefaultDataCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Executes all default initialization commands: modules, accesses, permissions, roles.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        if (!$application) {
            $output->writeln('<error>Application is not available</error>');

            return Command::FAILURE;
        }

        // ToDo:: refactor commands - use services and repositories instead hard coded query
        $commands = [
            'app:add-record-to-user-table',
            'app:add-record-to-module-table',
            'app:add-record-to-access-table',
            'app:add-record-to-permission-table',
            'app:add-record-to-role-table',
            'app:add-record-to-industry-table',
            'app:add-record-to-contract-type-table',
            'app:add-record-to-position-table',
            'app:add-record-to-notification-channel-setting-table',
        ];

        $output->writeln('***********************************************************');
        foreach ($commands as $commandName) {
            $output->writeln('--------------------------------------------------------------');
            $output->writeln("<info>Command execution: $commandName</info>");
            $command = $application->find($commandName);
            $result = $command->run(new ArrayInput([]), $output);

            if (Command::SUCCESS !== $result) {
                $output->writeln("<error>Error during execution: $commandName</error>");

                return $result;
            }

            $output->writeln('--------------------------------------------------------------');
        }

        $output->writeln('');
        $output->writeln('<info>All commands were executed successfully :)</info>');
        $output->writeln('');
        $output->writeln('***********************************************************');

        return Command::SUCCESS;
    }
}
