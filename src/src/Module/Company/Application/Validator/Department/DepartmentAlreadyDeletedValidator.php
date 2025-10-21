<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.department.restore.validator')]
final readonly class DepartmentAlreadyDeletedValidator implements ValidatorInterface
{
    public function __construct(private DepartmentReaderInterface $departmentReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'departmentUUID')) {
            return;
        }

        $uuid = $data->departmentUUID;
        $departmentDeleted = $this->departmentReaderRepository->getDeletedDepartmentByUUID($uuid);
        if (null === $departmentDeleted) {
            throw new \Exception($this->translator->trans('department.deleted.notExists', [':uuid' => $uuid], 'departments'), Response::HTTP_CONFLICT);
        }
    }
}
