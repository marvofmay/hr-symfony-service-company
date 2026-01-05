<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Validator\Constraints\Event;

use App\Module\System\Notification\Domain\Factory\NotificationEventFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValidNotificationEventsValidator extends ConstraintValidator
{
    public function __construct(private readonly NotificationEventFactory $factory, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidNotificationEvents) {
            throw new UnexpectedTypeException($constraint, ValidNotificationEvents::class);
        }

        if (!is_array($value)) {
            return;
        }

        $availableNames = array_map(
            fn ($event) => $event->getName(),
            $this->factory->all()
        );

        foreach ($value as $name) {
            if (!in_array($name, $availableNames, true)) {
                $message = $this->translator->trans(
                    $constraint->message['eventNotExists'],
                    [':invalidEvent' => $name],
                    $constraint->message['domain']
                );

                $this->context->buildViolation($message)->addViolation();
            }
        }
    }
}
