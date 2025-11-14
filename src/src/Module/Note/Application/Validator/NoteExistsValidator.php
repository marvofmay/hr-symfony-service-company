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
#[AutoconfigureTag('app.note.pdf.query.get.validator')]
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

        $user = $this->security->getUser();
        $noteUUID = $data->noteUUID;
        $noteExists = $this->noteReaderRepository->isNoteWithUUIDAndUserExists($noteUUID, $user);
        if (!$noteExists) {
            throw new \Exception($this->translator->trans('note.uuid.notExists', [':uuid' => $noteUUID], 'notes'), Response::HTTP_CONFLICT);
        }
    }
}
