<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\System\Domain\Enum\IndustryEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:add-record-to-industry-table')]
class AddRecordToIndustryTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to Industry table';
    private const string HELP = 'This command ensures that all IndustryEnum values exist in the Industry table';
    private const string SUCCESS_MESSAGE = 'Industry table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing industries';
    private const string INFO_NO_ADDED_MESSAGE = 'No new industries to add';

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
        $output->writeln('Checking and updating Industry table...');
        $industryRepository = $this->entityManager->getRepository(Industry::class);

        $existingIndustries = $industryRepository->createQueryBuilder('i')
            ->select('i.name')
            ->getQuery()
            ->getArrayResult();

        $existingNames = array_column($existingIndustries, 'name');
        $industriesToPersist = [];

        foreach (IndustryEnum::cases() as $enum) {
            if (!in_array($enum->value, $existingNames, true)) {
                $industry = new Industry();
                $industry->setName($enum->value);
                $industry->setDescription($this->translator->trans(
                    sprintf('industry.%s.description', $enum->value),
                    [],
                    'industry'
                ));
                $this->entityManager->persist($industry);
                $industriesToPersist[] = $enum->value;
            }
        }

        if (!empty($industriesToPersist)) {
            $this->entityManager->flush();
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $industriesToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}