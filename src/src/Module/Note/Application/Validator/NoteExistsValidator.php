<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Validator;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Note\Domain\Interface\NoteReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.note.update.validator')]
#[AutoconfigureTag('app.note.delete.validator')]
#[AutoconfigureTag('app.note.query.get.validator')]
final readonly class NoteExistsValidator implements ValidatorInterface
{
    public function __construct(
        private NoteReaderInterface $noteReaderRepository,
        private TranslatorInterface $translator,
        private Security $security
    )
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'noteUUID')) {
            return;
        }

        $employee = $this->security->getUser()->getEmployee();
        $noteUUID = $data->noteUUID;
        $noteExists = $this->noteReaderRepository->isNoteWithUUIDAndEmployeeExists($noteUUID, $employee);
        if (!$noteExists) {
            throw new \Exception($this->translator->trans('note.uuid.notFound', [':uuid' => $noteUUID], 'notes'), Response::HTTP_CONFLICT);
        }
    }
}
