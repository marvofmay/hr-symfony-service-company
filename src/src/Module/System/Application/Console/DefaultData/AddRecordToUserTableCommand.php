<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\Company\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-record-to-user-table')]
class AddRecordToUserTableCommand extends Command
{
    private const DESCRIPTION = 'Fills the User table with default data';
    private const HELP = 'This command allows you to populate the User table with default data.';
    private const SUCCESS_MESSAGE = 'User table has been filled with data successfully!';
    private const INFO_EXISTS = 'Default user already exists. No changes made.';
    private const CHECKING_INFO = 'Checking if default user exists...';
    private const DEFAULT_EMAIL = 'admin.hrapp@gmail.com';
    private const DEFAULT_PASSWORD = 'Admin123!';

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
        $output->writeln(self::CHECKING_INFO);

        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy([User::COLUMN_EMAIL => self::DEFAULT_EMAIL]);

        if ($existingUser !== null) {
            $output->writeln(sprintf('<comment>%s</comment>', self::INFO_EXISTS));
            return Command::SUCCESS;
        }

        $this->user->setEmail(self::DEFAULT_EMAIL);
        $this->user->setPassword(self::DEFAULT_PASSWORD);

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}