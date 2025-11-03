<?php
namespace App\Models;

use App\Support\Db;
use PDO;

class User
{
    public static function findByUsername(string $username): ?array
    {
        return Db::fetch(
            'SELECT * FROM users WHERE username = ? LIMIT 1',
            [$username]
        );
    }

    public static function findById(int $id): ?array
    {
        return Db::fetch(
            'SELECT * FROM users WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    public static function create(string $username, string $passwordHash): int
    {
        Db::query(
            'INSERT INTO users (username, password_hash, created_at) VALUES (?, ?, NOW())',
            [$username, $passwordHash]
        );
        
        return (int) Db::lastInsertId();
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}