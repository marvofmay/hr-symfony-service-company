<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(name: 'app:initialize-system-default-data')]
class InitializeSystemDefaultDataCommand extends Command
{
    public function __construct(
        #[AutowireIterator('app.command.initialize-system-default-data')] private readonly iterable $commands
    ) {
        parent::__construct();
    }

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

        foreach ($this->commands as $command) {
            $output->writeln('');
            $output->writeln('--------------------------------------------------------------');
            $output->writeln(sprintf('<info>Executing: %s</info>', $command->getName()));

            $result = $command->run(new ArrayInput([]), $output);
            if (Command::SUCCESS !== $result) {
                $output->writeln(sprintf('<error>Error during %s</error>', $command->getName()));

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
