<?php

namespace Core\TwoFactor;

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

class PhpAuthCodeMailer implements AuthCodeMailerInterface
{
    public function __construct(
        private mixed $mailer = null
    ) {
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $config = require base_path('config.php');
        $mailConfig = $config['mail'] ?? [];

        $recipient = $_SESSION['email'];
        $code = $user->getEmailAuthCode();
        $subject = $mailConfig['subject'] ?? 'Your authentication code';
        $from = $mailConfig['from'] ?? 'no-reply@localhost';

        if ($this->mailer && method_exists($this->mailer, 'send')) {
            $this->sendViaSymfonyMailer($recipient, $code, $subject, $from);
        } else {
            $this->sendViaPhpMail($recipient, $code, $subject, $from);
        }
    }

    private function sendViaSymfonyMailer($recipient, $code, $subject, $from)
    {
        $message = (new \Symfony\Component\Mime\Email())
            ->from($from)
            ->to($recipient)
            ->subject($subject)
            ->text("Your verification code is: {$code}\n\nEnter this code on the login page to complete authentication.");

        $this->mailer->send($message);
    }

    private function sendViaPhpMail($recipient, $code, $subject, $from)
    {
        $message = "Your verification code is: {$code}\n\nEnter this code on the login page to complete authentication.";
        $headers = "From: {$from}\r\n";
        @mail($recipient, $subject, $message, $headers);
    }
}
