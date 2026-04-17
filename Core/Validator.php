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

    public static function checkPasswordStrength($password)
    {
        $errors = [];

        if (strlen($password) < 12) {
            $errors[] = "Password must be at least 12 characters long.";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Must include at least one uppercase letter.";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Must include at least one lowercase letter.";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Must include at least one number.";
        }

        if (!preg_match('/[\W_]/', $password)) {
            $errors[] = "Must include at least one special character.";
        }

        return $errors;
    }
}
