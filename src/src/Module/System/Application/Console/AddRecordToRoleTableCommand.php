<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Module;
use App\Module\System\Domain\Enum\RoleEnum;
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
    private const string MODULE_NOT_FOUND_MESSAGE = 'Module "%s" does not exist. Aborting.';
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

        $moduleRepository = $this->entityManager->getRepository(Module::class);
        $roleRepository = $this->entityManager->getRepository(Role::class);

        $existingModules = $moduleRepository->createQueryBuilder(Module::ALIAS)
            ->select(Module::ALIAS. '.'. Module::COLUMN_UUID, Module::ALIAS. '.'. Module::COLUMN_NAME)
            ->getQuery()
            ->getArrayResult();

        $moduleMap = array_column($existingModules, Module::COLUMN_UUID, Module::COLUMN_NAME);

        $existingRoles = $roleRepository->createQueryBuilder(Access::ALIAS)
            ->select(Role::ALIAS . '.' . Role::COLUMN_NAME)
            ->getQuery()
            ->getArrayResult();

        $existingRolesNames = array_column($existingRoles, Access::COLUMN_NAME);
        $rolesToPersist = [];

        foreach (RoleEnum::cases() as $roleEnum) {
            [$moduleName, $roleName] = explode('.', $roleEnum->value, 2);

            if (!isset($moduleMap[$moduleName])) {
                $output->writeln(sprintf('<error>' . self::MODULE_NOT_FOUND_MESSAGE . '</error>', $moduleName));

                return Command::FAILURE;
            }

            if (!in_array($roleName, $existingRolesNames, true)) {
                $role = new Access();
                $role->setName($roleName);
                $role->setModule($this->entityManager->getRepository(Module::class)->findOneBy([Module::COLUMN_UUID => $moduleMap[$moduleName]]));
                $role->setDescription($this->translator->trans(
                    sprintf('role.%s.description', $roleEnum->value),
                    [],
                    'role'
                ));
                $role->setActive(true);
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
