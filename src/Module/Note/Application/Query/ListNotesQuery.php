<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Query;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Module\Note\Domain\Entity\Note;

class ListNotesQuery extends ListQueryAbstract implements QueryInterface
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    public function getAttributes(): array
    {
        return Note::getAttributes();
    }

    public function getRelations(): array
    {
        return Note::getRelations();
    }
}
