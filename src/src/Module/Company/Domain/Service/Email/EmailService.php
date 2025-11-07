<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Email;

use App\Module\System\Application\Command\Email\SendEmailCommand;
use App\Module\System\Domain\Entity\Email;
use App\Module\System\Domain\Interface\Email\EmailWriterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment as Twig;

final readonly class EmailService
{
    public function __construct(
        private EmailWriterInterface $emailWriterRepository,
        private Twig $twig,
        private MessageBusInterface $commandBus,
        private string $projectDir
    ) {}

    public function sendEmail(
        ?string $sender,
        array $recipients,
        string $subject,
        string $message = '',
        ?string $templateName = null,
        array $attachments = [],
        array $context = [],
    ): Email {

        $templateBody = null;
        if (null !== $templateName) {
            $logoPath = $this->projectDir . '/public/assets/images/hr-app-logo.png';
            if (file_exists($logoPath)) {
                $base64Logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                $context['logoCid'] = $base64Logo;
            }
            $templateBody = $this->twig->render($templateName, $context);
        }

        $email = Email::create(
            subject: $subject,
            recipients: $recipients,
            message: $message,
            templateName: $templateName,
            templateBody: $templateBody,
            sender: $sender,
        );

        if (null !== $templateBody) {
            $email->setRenderedTemplate($templateBody);
            $email->setContext($context);
        }

        foreach ($attachments as $file) {
            $email->addAttachment($file);
        }

        $this->emailWriterRepository->saveEmailInDB($email);

        $this->commandBus->dispatch(new SendEmailCommand($email->getUUID()));

        return $email;
    }
}