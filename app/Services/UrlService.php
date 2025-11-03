<?php
namespace App\Services;

use App\Models\ShortUrl;
use App\Support\Validator;

class UrlService
{
    public function createUrl(array $data): array
    {
        $errors = [];
        
        $longUrl = trim($data['long_url'] ?? '');
        $customCode = trim($data['custom_code'] ?? '');
        $expireAt = trim($data['expire_at'] ?? '');
        $isActive = isset($data['is_active']) ? (int) $data['is_active'] : 1;
        
        if (!Validator::required($longUrl)) {
            $errors[] = 'Long URL is required';
        } elseif (!Validator::url($longUrl)) {
            $errors[] = 'Invalid URL format';
        }
        
        if ($customCode) {
            if (!Validator::shortCode($customCode)) {
                $errors[] = 'Custom code must be 4-10 characters (letters, numbers, underscore, dash)';
            } elseif (ShortUrl::isCodeExists($customCode)) {
                $errors[] = 'Custom code already exists';
            }
        }
        
        if ($expireAt && !Validator::date($expireAt)) {
            $errors[] = 'Invalid expiry date format';
        } elseif ($expireAt && !Validator::futureDate($expireAt)) {
            $errors[] = 'Expiry date must be in the future';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $code = $customCode ?: ShortUrl::generateUniqueCode();
        $expireAt = $expireAt ?: null;
        
        try {
            $id = ShortUrl::create($code, $longUrl, $expireAt, $isActive);
            return ['success' => true, 'id' => $id, 'code' => $code];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => ['Failed to create short URL']];
        }
    }

    public function updateUrl(int $id, array $data): array
    {
        $errors = [];
        
        $url = ShortUrl::findById($id);
        if (!$url) {
            return ['success' => false, 'errors' => ['Short URL not found']];
        }
        
        $longUrl = trim($data['long_url'] ?? '');
        $expireAt = trim($data['expire_at'] ?? '');
        $isActive = isset($data['is_active']) ? (int) $data['is_active'] : 1;
        
        if (!Validator::required($longUrl)) {
            $errors[] = 'Long URL is required';
        } elseif (!Validator::url($longUrl)) {
            $errors[] = 'Invalid URL format';
        }
        
        if ($expireAt && !Validator::date($expireAt)) {
            $errors[] = 'Invalid expiry date format';
        } elseif ($expireAt && !Validator::futureDate($expireAt)) {
            $errors[] = 'Expiry date must be in the future';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $success = ShortUrl::update($id, $longUrl, $expireAt ?: null, $isActive);
            if ($success) {
                return ['success' => true];
            } else {
                return ['success' => false, 'errors' => ['Failed to update short URL']];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update short URL']];
        }
    }

    public function deleteUrl(int $id): array
    {
        $url = ShortUrl::findById($id);
        if (!$url) {
            return ['success' => false, 'errors' => ['Short URL not found']];
        }
        
        try {
            $success = ShortUrl::delete($id);
            if ($success) {
                return ['success' => true];
            } else {
                return ['success' => false, 'errors' => ['Failed to delete short URL']];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete short URL']];
        }
    }
}