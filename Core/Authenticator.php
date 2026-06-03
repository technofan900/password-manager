<?php

namespace Core;
use Core\App;
use Core\Database;

class Authenticator {

    public function attempt($login_data, $password) {
        $user = App::resolve(Database::class)->query('SELECT * FROM login WHERE username = :username OR email = :email', [
            'email' => $login_data,
            'username' => $login_data
        ])->find();

    if ($user && password_verify($password, $user['password'])){

        $this->login([
            'id' => $user['id'],
            'email' => $user['email'],
            'is_admin' => $user['is_admin'] ?? 0
            ]);

            return true;
        }

        return false;
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