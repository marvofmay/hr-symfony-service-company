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

#[AutoconfigureTag('app.notes.pdf.query.get.validator')]
final readonly class NotesExistsValidator implements ValidatorInterface
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
        $uuids = $data->notesUUIDs ?? [];
        $user = $this->security->getUser();

        if (empty($uuids)) {
            return;
        }

        $foundNotes = $this->noteReaderRepository
            ->getNotesByUUIDsAndUser($uuids, $user)
            ->map(fn ($note) => $note->getUUID())
            ->toArray();

        $missing = array_diff($uuids, $foundNotes);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('note.uuid.notExists', [':uuid' => $uuid], 'notes'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
