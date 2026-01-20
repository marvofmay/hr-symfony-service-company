<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Query\Message;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Module\System\Notification\Domain\Entity\NotificationRecipient;

class ListNotificationMessagesQuery extends ListQueryAbstract implements QueryInterface
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    public function getAttributes(): array
    {
        return NotificationRecipient::getAttributes();
    }

    public function getRelations(): array
    {
        return NotificationRecipient::getRelations();
    }
}
