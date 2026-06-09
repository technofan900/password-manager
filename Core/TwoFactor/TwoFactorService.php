<?php

namespace Core\TwoFactor;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGenerator;
use Core\TwoFactor\PhpAuthCodeMailer;
use Core\TwoFactor\SessionPersister;
use Core\TwoFactor\TwoFactorUser;
use Core\App;

class TwoFactorService
{
    public function __construct(
        protected int $codeLength = 6
    ) {
    }

    public function generateEmailCodeForUser(int $userId, string $email): void
    {
        $user = new TwoFactorUser($userId, $email, true);

        $mailer = $this->getMailer();
        $generator = new CodeGenerator(new SessionPersister(), new PhpAuthCodeMailer($mailer), $this->codeLength);
        $generator->generateAndSend($user);
    }

    protected function getMailer()
    {
        if (class_exists('Symfony\Component\Mailer\MailerInterface')) {
            return App::resolve('Symfony\Component\Mailer\MailerInterface');
        }

        // Fallback: return a null mailer placeholder (uses php mail())
        return null;
    }

    public function hasPendingUser(): bool
    {
        return ! empty($_SESSION['_two_factor']['user_id']);
    }

    public function getPendingUserId(): ?int
    {
        return isset($_SESSION['_two_factor']['user_id']) ? (int) $_SESSION['_two_factor']['user_id'] : null;
    }

    public function getPendingEmail(): ?string
    {
        return $_SESSION['_two_factor']['email'] ?? null;
    }

    public function isCodeValid(string $code): bool
    {
        if (empty($_SESSION['_two_factor']['code']) || empty($_SESSION['_two_factor']['expires_at'])) {
            return false;
        }

        if (time() > $_SESSION['_two_factor']['expires_at']) {
            $this->clear();
            return false;
        }

        return hash_equals((string) $_SESSION['_two_factor']['code'], trim($code));
    }

    public function clear(): void
    {
        unset($_SESSION['_two_factor']);
    }
}
