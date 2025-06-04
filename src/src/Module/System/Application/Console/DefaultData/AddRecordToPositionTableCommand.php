<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData;

use App\Module\Company\Domain\Entity\Position;
use App\Module\System\Domain\Enum\PositionEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(name: 'app:add-record-to-position-table')]
class AddRecordToPositionTableCommand extends Command
{
    private const string DESCRIPTION = 'Add missing records to Position table';
    private const string HELP = 'This command ensures that all TechnologyPositionEnum values exist in the Position table';
    private const string SUCCESS_MESSAGE = 'Position table has been updated successfully!';
    private const string INFO_ADDED_MESSAGE = 'Added missing positions';
    private const string INFO_NO_ADDED_MESSAGE = 'No new positions to add';

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
        $output->writeln('Checking and updating Position table...');
        $positionRepository = $this->entityManager->getRepository(Position::class);

        $existingPositions = $positionRepository->createQueryBuilder('p')
            ->select('p.name')
            ->getQuery()
            ->getArrayResult();

        $existingNames = array_column($existingPositions, 'name');
        $positionsToPersist = [];

        foreach (PositionEnum::cases() as $enum) {
            if (!in_array($enum->value, $existingNames, true)) {
                $position = new Position();
                $position->setName($enum->value);
                $position->setActive(true);
                $position->setDescription($this->translator->trans(
                    sprintf('position.%s.description', $enum->value),
                    [],
                    'position'
                ));
                $this->entityManager->persist($position);
                $positionsToPersist[] = $enum->value;
            }
        }

        if (!empty($positionsToPersist)) {
            $this->entityManager->flush();
            $output->writeln(sprintf('<info>%s: %s.</info>', self::INFO_ADDED_MESSAGE, implode(', ', $positionsToPersist)));
            $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));
        } else {
            $output->writeln(sprintf('<info>%s</info>', self::INFO_NO_ADDED_MESSAGE));
        }

        return Command::SUCCESS;
    }
}
