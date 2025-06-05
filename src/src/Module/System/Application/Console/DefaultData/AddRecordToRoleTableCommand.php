<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Application\Console\DefaultData\Data\RoleEnum;
use App\Module\System\Domain\Entity\Access;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:add-record-to-role-table')]
class AddRecordToRoleTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to Role table';
    private const string HELP = 'This command ensures that all RoleEnum values exist in the Role table';
    private const string SUCCESS_MESSAGE = 'Role table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing roles';
    private const string INFO_NO_ADDED_MESSAGE = 'No new roles to add';

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
        $output->writeln('Checking and updating Roles table...');
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $existingRoles = $roleRepository->createQueryBuilder(Role::ALIAS)
            ->select(Role::ALIAS . '.' . Role::COLUMN_NAME)
            ->getQuery()
            ->getArrayResult();

        $existingRolesNames = array_column($existingRoles, Access::COLUMN_NAME);
        $rolesToPersist = [];

        foreach (RoleEnum::cases() as $roleEnum) {
            $roleName = $roleEnum->value;
            $translatedName = $this->translator->trans(sprintf('role.defaultData.name.%s', $roleName), [], 'roles');
            if (!in_array($translatedName, $existingRolesNames, true)) {
                $role = new Role();
                $role->setName($translatedName);
                $role->setDescription($this->translator->trans(sprintf('role.defaultData.description.%s', $roleName), [], 'roles'));
                $this->entityManager->persist($role);
                $rolesToPersist[] = $roleEnum->value;
            }
        }

        if (!empty($rolesToPersist)) {
            $this->entityManager->flush();
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $rolesToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
