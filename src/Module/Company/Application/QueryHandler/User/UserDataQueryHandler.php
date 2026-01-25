<?php

declare(strict_types=1);

namespace App\Module\Company\Application\QueryHandler\User;

use App\Module\Company\Application\Query\User\UserDataQuery;
use App\Module\Company\Application\ReadModel\Employee\EmployeeView;
use App\Module\Company\Application\ReadModel\User\MeView;
use App\Module\Company\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsMessageHandler(bus: 'query.bus')]
readonly class UserDataQueryHandler
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function __invoke(UserDataQuery $query): MeView
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        $employee = $user->getEmployee();

        return new MeView(
            userUUID: $user->getUUID()->toString(),
            email: $user->getEmail(),
            employee: $employee
                ? EmployeeView::fromEmployee($employee)
                : null
        );
    }
}
