<?php


namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


class MailService
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send_mail(string $from, string $subject, string $htmlTemplate, array $context, string $to = 'admin@recettes-faciles.fr'): void
    {
        //mails
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        $this->mailer->send($email);
    }
}
