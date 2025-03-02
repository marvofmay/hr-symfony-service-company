<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

class ContractTypesQueryDTO
{
    #[OA\Property(description: 'Nazwa formy zatrudnienia', type: 'string', nullable: true)]
    public ?string $name = null;

    #[OA\Property(description: 'Opis formy zatrudnienia', type: 'string', nullable: true)]
    public ?string $description = null;

    #[OA\Property(description: 'Data utworzenia', type: 'string', format: 'date-time', nullable: true)]
    public ?string $createdAt = null;

    #[OA\Property(description: 'Data aktualizacji', type: 'string', format: 'date-time', nullable: true)]
    public ?string $updatedAt = null;

    #[OA\Property(description: 'Data usunięcia', type: 'string', format: 'date-time', nullable: true)]
    public ?string $deletedAt = null;

    #[OA\Property(description: 'Numer strony wyników', type: 'integer', default: 1, nullable: true)]
    public ?int $page = 1;

    #[OA\Property(description: 'Liczba wyników na stronę', type: 'integer', default: 10, nullable: true)]
    public ?int $pageSize = 10;

    #[OA\Property(description: 'Pole do sortowania', type: 'string', enum: ['name', 'description', 'createdAt', 'updatedAt', 'deletedAt'])]
    public ?string $sortBy = 'createdAt';

    #[OA\Property(description: 'Kierunek sortowania', type: 'string', default: 'asc', enum: ['asc', 'desc'])]
    public ?string $sortDirection = 'desc';

    #[OA\Property(description: 'Flaga pobierania usuniętych ról', type: 'integer', default: 0, enum: [0, 1])]
    public ?int $deleted = null;

    #[OA\Property(description: 'Fraza do wyszukania', type: 'string', nullable: true)]
    public ?string $phrase = null;
}
