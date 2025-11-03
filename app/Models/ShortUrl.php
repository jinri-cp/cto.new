<?php
namespace App\Models;

use App\Support\Db;
use PDO;

class ShortUrl
{
    public static function findByCode(string $code): ?array
    {
        return Db::fetch(
            'SELECT * FROM short_urls WHERE code = ? LIMIT 1',
            [$code]
        );
    }

    public static function findById(int $id): ?array
    {
        return Db::fetch(
            'SELECT * FROM short_urls WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    public static function create(string $code, string $longUrl, ?string $expireAt = null, int $isActive = 1): int
    {
        Db::query(
            'INSERT INTO short_urls (code, long_url, is_active, expire_at, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())',
            [$code, $longUrl, $isActive, $expireAt]
        );
        
        return (int) Db::lastInsertId();
    }

    public static function update(int $id, string $longUrl, ?string $expireAt = null, int $isActive = 1): bool
    {
        $result = Db::query(
            'UPDATE short_urls SET long_url = ?, is_active = ?, expire_at = ?, updated_at = NOW() WHERE id = ?',
            [$longUrl, $isActive, $expireAt, $id]
        );
        
        return $result->rowCount() > 0;
    }

    public static function delete(int $id): bool
    {
        $result = Db::query('DELETE FROM short_urls WHERE id = ?', [$id]);
        return $result->rowCount() > 0;
    }

    public static function isCodeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) as count FROM short_urls WHERE code = ?';
        $params = [$code];
        
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        
        $result = Db::fetch($sql, $params);
        return (int) $result['count'] > 0;
    }

    public static function generateUniqueCode(int $length = 6): string
    {
        do {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $code = substr(str_shuffle($chars), 0, $length);
        } while (self::isCodeExists($code));
        
        return $code;
    }

    public static function getPaginated(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        
        $where = '';
        $params = [];
        
        if ($search) {
            $where = 'WHERE code LIKE ? OR long_url LIKE ?';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $sql = "SELECT * FROM short_urls {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $urls = Db::fetchAll($sql, $params);
        
        $countSql = "SELECT COUNT(*) as total FROM short_urls {$where}";
        $countParams = array_slice($params, 0, -2);
        $total = (int) Db::fetch($countSql, $countParams)['total'];
        
        return [
            'data' => $urls,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }

    public static function isValid(string $code): bool
    {
        $url = self::findByCode($code);
        
        if (!$url) {
            return false;
        }
        
        if ($url['is_active'] == 0) {
            return false;
        }
        
        if ($url['expire_at'] && strtotime($url['expire_at']) < time()) {
            return false;
        }
        
        return true;
    }
}