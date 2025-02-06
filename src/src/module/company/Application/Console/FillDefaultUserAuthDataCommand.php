<?php

declare(strict_types = 1);

namespace App\module\company\Application\Console;

use App\module\company\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fill-default-user-auth-data')]
class FillDefaultUserAuthDataCommand extends Command
{
    private const DESCRIPTION = 'Fills the User table with default data';
    private const HELP = 'This command allows you to populate the User table with default data.';
    private const SUCCESS_MESSAGE = 'User table has been filled with data successfully!';

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly User $user)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->setHelp(self::HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Filling Employee table with data...');
        $this->user->setEmail('admin.hrapp@gmail.com');
        $this->user->setPassword('Admin123!');
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}
