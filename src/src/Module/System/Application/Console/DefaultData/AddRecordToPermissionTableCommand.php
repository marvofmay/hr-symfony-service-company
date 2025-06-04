<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\System\Domain\Entity\Permission;
use App\Module\System\Domain\Enum\PermissionEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:add-record-to-permission-table')]
class AddRecordToPermissionTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to Permission table';
    private const string HELP = 'This command ensures that all PermissionEnum values exist in the Permission table';
    private const string SUCCESS_MESSAGE = 'Permission table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing permissions';
    private const string INFO_NO_ADDED_MESSAGE = 'No new permissions to add';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslatorInterface $translator
    ) {
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
        $output->writeln('Checking and updating Permission table...');

        $permissionRepository = $this->entityManager->getRepository(Permission::class);
        $existingPermissions = $permissionRepository->createQueryBuilder(Permission::ALIAS)
            ->select(Permission::ALIAS . '.' . Permission::COLUMN_NAME)
            ->getQuery()
            ->getArrayResult();

        $existingPermissionNames = array_column($existingPermissions, Permission::COLUMN_NAME);

        $permissionsToPersist = [];

        foreach (PermissionEnum::cases() as $permissionEnum) {
            if (!in_array($permissionEnum->value, $existingPermissionNames, true)) {
                $permission = new Permission();
                $permission->setName($permissionEnum->value);
                $permission->setDescription($this->translator->trans(
                    sprintf('permission.%s.description', $permissionEnum->value),
                    [],
                    'permissions'
                ));
                $permission->setActive(true);
                $this->entityManager->persist($permission);
                $permissionsToPersist[] = $permissionEnum->value;
            }
        }

        if (!empty($permissionsToPersist)) {
            $this->entityManager->flush();
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $permissionsToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
