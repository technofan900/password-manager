<?php

namespace Core;
use Core\App;
use Core\Database;
use Core\TwoFactor\TwoFactorService;

class Authenticator {

    public function attempt($login_data, $password) {
        $user = App::resolve(Database::class)->query('SELECT * FROM login WHERE username = :username OR email = :email', [
            'email' => $login_data,
            'username' => $login_data
        ])->find();

        if ($user && password_verify($password, $user['password'])) {
            if ($this->isTwoFactorEnabled()) {
                $this->startTwoFactor($user);
                return '2fa_required';
            }

            $this->login([
                'id' => $user['id'],
                'email' => $user['email'],
                'is_admin' => $user['is_admin'] ?? 0
            ]);

            return true;
        }

        return false;
    }

    public function verifyTwoFactorCode($code) {
        $twoFactor = new TwoFactorService();
        if (! $twoFactor->isCodeValid($code)) {
            return false;
        }

        $userId = $twoFactor->getPendingUserId();
        if (! $userId) {
            return false;
        }

        $user = App::resolve(Database::class)->query('SELECT * FROM login WHERE id = :id', [
            'id' => $userId
        ])->find();

        if (! $user) {
            $twoFactor->clear();
            return false;
        }

        $this->login([
            'id' => $user['id'],
            'email' => $user['email'],
            'is_admin' => $user['is_admin'] ?? 0
        ]);

        $twoFactor->clear();
        return true;
    }

    protected function startTwoFactor($user)
    {
        $config = require base_path('config.php');
        $codeLength = intval($config['app']['email_two_factor_code_length'] ?? 6);
        $service = new TwoFactorService($codeLength);
        $service->generateEmailCodeForUser((int) $user['id'], $user['email']);
    }

    protected function isTwoFactorEnabled(): bool
    {
        $config = require base_path('config.php');
        return $config['app']['email_two_factor_enabled'] ?? true;
    }

    public function adminAttempt($login, $password) {
        $admin = App::resolve(Database::class)->query("SELECT * FROM admin WHERE username = :username", [
            'username' => $login
        ])->find();

        if ($admin && password_verify($password, $admin['username'])) {
            $this->login([
                'id' => $admin['id'],
                'admin' => $admin['username']
            ]);
            return true;
        }

        return false;
    }

    public function login ($user) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email']
        ];
        
        // Set admin session flag if user is admin
        if (isset($user['is_admin']) && $user['is_admin']) {
            $_SESSION['admin'] = true;
        }
        
        session_regenerate_id(true);

        // Auto-fill persisted password settings for the user (or global fallback)
        $storageDir = __DIR__ . '/../storage';
        $filePath = $storageDir . '/password_settings.json';

        if (file_exists($filePath)) {
            $json = @file_get_contents($filePath);
            $data = $json ? json_decode($json, true) : null;
            if (is_array($data)) {
                $userId = $user['id'] ?? null;
                if ($userId && isset($data[(string) $userId])) {
                    $_SESSION['password_settings'] = $data[(string) $userId];
                } elseif (isset($data['global'])) {
                    $_SESSION['password_settings'] = $data['global'];
                }
            }
        }
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']); 
    }
    

}