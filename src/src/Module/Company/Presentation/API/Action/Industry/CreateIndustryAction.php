<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Application\Validator\Industry\IndustryValidator;
use App\Module\Company\Domain\DTO\Industry\CreateDTO;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateIndustryAction
{
    public function __construct(private MessageBusInterface $commandBus, private IndustryValidator $industryValidator)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        try {
            $this->industryValidator->isIndustryNameAlreadyExists($createDTO->name);

            $this->commandBus->dispatch(new CreateIndustryCommand($createDTO->name, $createDTO->description));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
