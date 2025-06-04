<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Module;
use App\Module\System\Domain\Enum\AccessEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:add-record-to-access-table')]
class AddRecordToAccessTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to Access table';
    private const string HELP = 'This command ensures that all AccessEnum values exist in the Access table';
    private const string SUCCESS_MESSAGE = 'Access table has been updated successfully!';
    private const string MODULE_NOT_FOUND_MESSAGE = 'Module "%s" does not exist. Aborting.';
    private const string INFO_ADDED_MESSAGE = 'Added missing accesses';
    private const string INFO_NO_ADDED_MESSAGE = 'No new accesses to add';

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
        $output->writeln('Checking and updating Access table...');

        $moduleRepository = $this->entityManager->getRepository(Module::class);
        $accessRepository = $this->entityManager->getRepository(Access::class);

        $existingModules = $moduleRepository->createQueryBuilder(Module::ALIAS)
            ->select(Module::ALIAS. '.'. Module::COLUMN_UUID, Module::ALIAS. '.'. Module::COLUMN_NAME)
            ->getQuery()
            ->getArrayResult();

        $moduleMap = array_column($existingModules, Module::COLUMN_UUID, Module::COLUMN_NAME);

        $existingAccesses = $accessRepository->createQueryBuilder(Access::ALIAS)
            ->select(Access::ALIAS . '.' . Access::COLUMN_NAME)
            ->getQuery()
            ->getArrayResult();

        $existingAccessNames = array_column($existingAccesses, Access::COLUMN_NAME);
        $accessesToPersist = [];

        foreach (AccessEnum::cases() as $accessEnum) {
            [$moduleName, $accessName] = explode('.', $accessEnum->value, 2);

            if (!isset($moduleMap[$moduleName])) {
                $output->writeln(sprintf('<error>' . self::MODULE_NOT_FOUND_MESSAGE . '</error>', $moduleName));

                return Command::FAILURE;
            }

            if (!in_array($accessName, $existingAccessNames, true)) {
                $access = new Access();
                $access->setName($accessName);
                $access->setModule($this->entityManager->getRepository(Module::class)->findOneBy([Module::COLUMN_UUID => $moduleMap[$moduleName]]));
                $access->setDescription($this->translator->trans(
                    sprintf('access.%s.description', $accessEnum->value),
                    [],
                    'accesses'
                ));
                $access->setActive(true);
                $this->entityManager->persist($access);
                $accessesToPersist[] = $accessEnum->value;
            }
        }

        if (!empty($accessesToPersist)) {
            $this->entityManager->flush();
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $accessesToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
