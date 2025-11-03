<?php
namespace App\Support;

class Csrf
{
    public static function generateToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function refreshToken(): string
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    public static function getHiddenField(): string
    {
        $token = self::generateToken();
        $config = require __DIR__ . '/../../config/app.php';
        $fieldName = $config['csrf_token_name'];
        
        return sprintf('<input type="hidden" name="%s" value="%s">', $fieldName, $token);
    }
}