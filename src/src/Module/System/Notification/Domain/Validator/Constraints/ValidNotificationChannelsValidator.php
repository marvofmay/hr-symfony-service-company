<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Validator\Constraints;

use App\Module\System\Notification\Domain\Factory\NotificationChannelFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValidNotificationChannelsValidator  extends ConstraintValidator
{
    public function __construct(private readonly NotificationChannelFactory $factory, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidNotificationChannels) {
            throw new UnexpectedTypeException($constraint, ValidNotificationChannels::class);
        }

        if (!is_array($value)) {
            return;
        }

        $availableCodes = array_map(
            fn($channel) => $channel->getCode(),
            $this->factory->all()
        );

        foreach ($value as $code) {
            if (!in_array($code, $availableCodes, true)) {
                $message = $this->translator->trans(
                    $constraint->message['channelNotExists'],
                    [':invalidChannel' => $code],
                    $constraint->message['domain']
                );

                $this->context->buildViolation($message)->addViolation();
            }
        }
    }
}