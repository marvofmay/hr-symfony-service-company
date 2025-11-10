<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Query\Event;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;

final class ListNotificationEventSettingQuery extends ListQueryAbstract
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    public function getAttributes(): array
    {
        return NotificationEventSetting::getAttributes();
    }

    public function getRelations(): array
    {
        return NotificationEventSetting::getRelations();
    }
}
