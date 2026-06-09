<?php

namespace Core\TwoFactor;

use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

class TwoFactorUser implements TwoFactorInterface
{
    private ?string $emailAuthCode = null;

    public function __construct(
        private int $id,
        private string $email,
        private bool $emailAuthEnabled = true,
    ) {
    }

    public function isEmailAuthEnabled(): bool
    {
        return $this->emailAuthEnabled;
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): ?string
    {
        return $this->emailAuthCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->emailAuthCode = $authCode;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
