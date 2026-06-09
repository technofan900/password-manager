<?php

namespace Core\TwoFactor;

use Scheb\TwoFactorBundle\Model\PersisterInterface;

class SessionPersister implements PersisterInterface
{
    public function persist(object $user): void
    {
        if (! $user instanceof TwoFactorUser) {
            return;
        }

        $_SESSION['_two_factor'] = [
            'user_id' => $user->getId(),
            'email' => $user->getEmailAuthRecipient(),
            'code' => $user->getEmailAuthCode(),
            'expires_at' => time() + 300,
        ];
    }
}
