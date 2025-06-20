<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class IndustryMultipleCreator
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function multipleCreate(array $data): void
    {
        $industries = new ArrayCollection();
        foreach ($data as $item) {
            $industry = new Industry();
            $industry->setName($item[ImportIndustriesFromXLSX::COLUMN_NAME]);
            $industry->setDescription($item[ImportIndustriesFromXLSX::COLUMN_DESCRIPTION]);

            $industries[] = $industry;
        }

        $this->industryWriterRepository->saveIndustriesInDB($industries);
    }
}
