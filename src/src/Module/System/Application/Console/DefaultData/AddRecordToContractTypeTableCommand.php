<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\System\Application\Console\DefaultData\Data\ContractTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:add-record-to-contract-type-table')]
class AddRecordToContractTypeTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to ContractType table';
    private const string HELP = 'This command ensures that all ContractTypeEnum values exist in the ContractType table';
    private const string SUCCESS_MESSAGE = 'ContractType table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing contract types';
    private const string INFO_NO_ADDED_MESSAGE = 'No new contract types to add';

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
        $output->writeln('Checking and updating ContractType table...');
        $contractTypeRepository = $this->entityManager->getRepository(ContractType::class);

        $existingContractTypes = $contractTypeRepository->createQueryBuilder(ContractType::ALIAS)
            ->select(ContractType::ALIAS . '.name')
            ->getQuery()
            ->getArrayResult();

        $existingNames = array_column($existingContractTypes, 'name');
        $contractTypesToPersist = [];

        foreach (ContractTypeEnum::cases() as $enum) {
            $translatedName = $this->translator->trans(sprintf('contractType.defaultData.name.%s', $enum->value), [], 'contract_types');
            if (!in_array($translatedName, $existingNames, true)) {
                $contractType = new ContractType();
                $contractType->setName($translatedName);
                $contractType->setActive(true);
                $contractType->setDescription($this->translator->trans(
                    sprintf('contractType.defaultData.description.%s', $enum->value),
                    [],
                    'contract_types'
                ));
                $this->entityManager->persist($contractType);
                $contractTypesToPersist[] = $enum->value;
            }
        }

        if (!empty($contractTypesToPersist)) {
            $this->entityManager->flush();
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $contractTypesToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
