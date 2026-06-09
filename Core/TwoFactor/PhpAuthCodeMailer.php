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

        $recipient = null;
        if (method_exists($user, 'getEmailAuthRecipient')) {
            $recipient = $user->getEmailAuthRecipient();
        }
        if (empty($recipient) && isset($_SESSION['email'])) {
            $recipient = $_SESSION['email'];
        }

        $code = $user->getEmailAuthCode();
        $subject = $mailConfig['subject'] ?? 'Your authentication code';
        $from = $mailConfig['from'] ?? 'no-reply@localhost';

        if (empty($recipient)) {
            // Nothing to send to
            return;
        }

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

        try {
            $this->mailer->send($message);
            $this->logMailDebug("symfony: sent to {$recipient} subject={$subject}");
        } catch (\Throwable $ex) {
            $this->logMailDebug("symfony: failed to send to {$recipient} subject={$subject} error=" . $ex->getMessage());
        }
    }

    private function sendViaPhpMail($recipient, $code, $subject, $from)
    {
        $message = "Your verification code is: {$code}\n\nEnter this code on the login page to complete authentication.";
        $headers = "From: {$from}\r\n";
        try {
            $ok = @mail($recipient, $subject, $message, $headers);
            if ($ok) {
                $this->logMailDebug("phpmail: sent to {$recipient} subject={$subject}");
            } else {
                $this->logMailDebug("phpmail: failed to send to {$recipient} subject={$subject}");
            }
        } catch (\Throwable $ex) {
            $this->logMailDebug("phpmail: exception sending to {$recipient} subject={$subject} error=" . $ex->getMessage());
        }
    }

    private function logMailDebug($message)
    {
        $path = base_path('storage/mail_errors.log');
        $ts = date('c');
        @file_put_contents($path, "[{$ts}] {$message}\n", FILE_APPEND | LOCK_EX);
    }
}
