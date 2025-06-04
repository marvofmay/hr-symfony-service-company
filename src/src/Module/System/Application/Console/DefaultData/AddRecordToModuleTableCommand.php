<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\System\Domain\Entity\Module;
use App\Module\System\Domain\Enum\ModuleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:add-record-to-module-table')]
class AddRecordToModuleTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to Module table';
    private const string HELP = 'This command ensures that all ModuleEnum values exist in the Module table';
    private const string SUCCESS_MESSAGE = 'Module table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing modules';
    private const string INFO_NO_ADDED_MESSAGE = 'No new modules to add';

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
        $output->writeln('Checking and updating Module table...');

        $moduleRepository = $this->entityManager->getRepository(Module::class);
        $existingModules = $moduleRepository->createQueryBuilder(Module::ALIAS)
            ->select(Module::ALIAS . '.' . Module::COLUMN_NAME)
            ->getQuery()
            ->getArrayResult();

        $existingModuleNames = array_column($existingModules, Module::COLUMN_NAME);

        $modulesToPersist = [];

        foreach (ModuleEnum::cases() as $moduleEnum) {
            if (!in_array($moduleEnum->value, $existingModuleNames, true)) {
                $module = new Module();
                $module->setName($moduleEnum->value);
                $module->setDescription($this->translator->trans(
                    sprintf('module.%s.description', $moduleEnum->value),
                    [],
                    'modules'
                ));
                $module->setActive(true);
                $this->entityManager->persist($module);
                $modulesToPersist[] = $moduleEnum->value;
            }
        }

        if (!empty($modulesToPersist)) {
            $this->entityManager->flush();
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $modulesToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
