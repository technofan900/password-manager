<?php
namespace Core;

class Validator
{
    public static function string($value, $min = 1, $max = INF)
    {
        $value = trim($value);
        return strlen($value) >= $min && strlen($value) <= $max;
    }

    public static function email ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // public static function checkPasswordStrength($password)
    // {
    //     $errors = [];

    //     if (strlen($password) < 12) {
    //         $errors[] = "Password must be at least 12 characters long.";
    //     }

    //     if (!preg_match('/[A-Z]/', $password)) {
    //         $errors[] = "Must include at least one uppercase letter.";
    //     }

    //     if (!preg_match('/[a-z]/', $password)) {
    //         $errors[] = "Must include at least one lowercase letter.";
    //     }

    //     if (!preg_match('/[0-9]/', $password)) {
    //         $errors[] = "Must include at least one number.";
    //     }

    //     if (!preg_match('/[\W_]/', $password)) {
    //         $errors[] = "Must include at least one special character.";
    //     }

    //     return $errors;
    // }

    public static function checkPasswordStrength($password, $rules = null)
    {
        $errors = [];

        // If rules provided, use them; otherwise use strict default rules
        if ($rules === null) {
            $rules = [
                'min_length' => 12,
                'require_uppercase' => true,
                'require_lowercase' => true,
                'require_numbers' => true,
                'require_special' => true,
            ];
        }

        $min = $rules['min_length'] ?? 0;
        $reqUpper = $rules['require_uppercase'] ?? false;
        $reqLower = $rules['require_lowercase'] ?? false;
        $reqNums = $rules['require_numbers'] ?? false;
        $reqSpec = $rules['require_special'] ?? false;

        if ($min > 0 && strlen($password) < $min) {
            $errors[] = "Password must be at least {$min} characters long.";
        }

        if ($reqUpper && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Must include at least one uppercase letter.";
        }

        if ($reqLower && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Must include at least one lowercase letter.";
        }

        if ($reqNums && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Must include at least one number.";
        }

        if ($reqSpec && !preg_match('/[\W_]/', $password)) {
            $errors[] = "Must include at least one special character.";
        }

        return $errors;
    }

    public static function setSavedPasswordStrength($values)
    {
        // Convert submitted checkbox values into a consistent rule set
        $rules = [
            'min_length' => !empty($values['12-numbers']) ? 12 : 0,
            'require_uppercase' => !empty($values['uppercase-letter']),
            'require_numbers' => !empty($values['numbers']),
            'require_special' => !empty($values['special-sym']),
            // always keep lowercase check enabled for saved passwords
            'require_lowercase' => true,
        ];

        // Persist to session so other controllers can read it
        $_SESSION['password_settings'] = $rules;

        // Also persist to disk so settings survive logout.
        // Store per-user when logged in; otherwise store as a global default.
        $storageDir = __DIR__ . '/../storage';
        $filePath = $storageDir . '/password_settings.json';

        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }

        $data = [];
        if (file_exists($filePath)) {
            $json = @file_get_contents($filePath);
            $decoded = $json ? json_decode($json, true) : null;
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $userId = $_SESSION['user']['id'] ?? null;
        $key = $userId ? (string) $userId : 'global';
        $data[$key] = $rules;

        @file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        return $rules;
    }
}
