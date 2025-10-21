<?php

declare(strict_types=1);

namespace App\Common\Application\Factory;

use App\Common\Domain\Exception\TransformerNotFoundException;
use App\Module\Company\Application\QueryHandler\Company\ListCompaniesQueryHandler;
use App\Module\Company\Application\QueryHandler\ContractType\ListContractTypesQueryHandler;
use App\Module\Company\Application\QueryHandler\Department\ListDepartmentsQueryHandler;
use App\Module\Company\Application\QueryHandler\Employee\ListEmployeesQueryHandler;
use App\Module\Company\Application\QueryHandler\Industry\ListIndustriesQueryHandler;
use App\Module\Company\Application\QueryHandler\Position\ListPositionsQueryHandler;
use App\Module\Company\Application\QueryHandler\Role\ListRolesQueryHandler;
use App\Module\Company\Application\Transformer\Company\CompanyDataTransformer;
use App\Module\Company\Application\Transformer\ContractType\ContractTypeDataTransformer;
use App\Module\Company\Application\Transformer\Department\DepartmentDataTransformer;
use App\Module\Company\Application\Transformer\Employee\EmployeeDataTransformer;
use App\Module\Company\Application\Transformer\Industry\IndustryDataTransformer;
use App\Module\Company\Application\Transformer\Position\PositionDataTransformer;
use App\Module\Company\Application\Transformer\Role\RoleDataTransformer;
use App\Module\Note\Application\QueryHandler\ListNotesQueryHandler;
use App\Module\Note\Application\Transformer\NoteDataTransformer;

class TransformerFactory
{
    public static function createForHandler(string $handlerClass): object
    {
        return match ($handlerClass) {
            ListCompaniesQueryHandler::class => new CompanyDataTransformer(),
            ListDepartmentsQueryHandler::class => new DepartmentDataTransformer(),
            ListEmployeesQueryHandler::class => new EmployeeDataTransformer(),
            ListRolesQueryHandler::class => new RoleDataTransformer(),
            ListIndustriesQueryHandler::class => new IndustryDataTransformer(),
            ListPositionsQueryHandler::class => new PositionDataTransformer(),
            ListContractTypesQueryHandler::class => new ContractTypeDataTransformer(),
            ListNotesQueryHandler::class => new NoteDataTransformer(),
            default => throw new TransformerNotFoundException("No transformer found for handler: {$handlerClass}"),
        };
    }
}
