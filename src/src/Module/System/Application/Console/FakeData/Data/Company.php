<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\FakeData\Data;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\System\Application\Console\DefaultData\Data\IndustryEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class Company
{
    public function __construct(private EntityManagerInterface $entityManager, private TranslatorInterface $translator)
    {
    }

    public function getDefaultData(): array
    {
        $translatedTechnologyName = $this->translator->trans(sprintf('industry.defaultData.name.%s', IndustryEnum::TECHNOLOGY->value), [], 'industries');
        $technologyUUID = $this->entityManager->getRepository(Industry::class)->findOneBy(['name' => $translatedTechnologyName])->getUuid();

        return [
            'fullName' => 'Feture Technology',
            'shortName' => 'FT',
            'nip' => '9316831327',
            'regon' => '9316831327',
            'description' => '',
            'industryUUID' =>  $technologyUUID,
            'active' => true,
            'phones' => [
                '155-555-555',
                '255-555-555',
                '355-555-555',
            ],
            'emails' => [
                'futuretechnnology_1@example.com',
                'futuretechnnology_2@example.com',
                'futuretechnnology_3@example.com',
            ],
            'websites' => [
                'http://futuretechnology_1.com',
                'http://futuretechnology_2.com',
            ],
            'address' => [
                'street' => 'Wiejska 1',
                'postcode' => '11-111',
                'city' => 'Gdańsk',
                'country' => 'Polska',
                'active' => true,
            ],
        ];
    }
}