<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Module;

use App\Module\System\Domain\Enum\ModuleEnum;
use App\Module\System\Domain\Interface\Module\ModuleReaderInterface;

readonly class ModuleChecker
{
    public function __construct(private ModuleReaderInterface $moduleReaderRepository)
    {
    }

    public function checkIsExists(ModuleEnum $moduleEnum): bool
    {
        return $this->moduleReaderRepository->isModuleWithNameExists($moduleEnum->value);
    }

    public function checkIsActive(ModuleEnum $moduleEnum): bool
    {
        return $this->moduleReaderRepository->isModuleActive($moduleEnum->value);
    }
}