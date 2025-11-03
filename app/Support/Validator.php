<?php
namespace App\Support;

class Validator
{
    public static function url(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false && 
               in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    public static function shortCode(string $code): bool
    {
        return preg_match('/^[A-Za-z0-9_-]{4,10}$/', $code) === 1;
    }

    public static function required(string $value): bool
    {
        return !empty(trim($value));
    }

    public static function maxLength(string $value, int $length): bool
    {
        return strlen($value) <= $length;
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function date(string $date): bool
    {
        return strtotime($date) !== false;
    }

    public static function futureDate(string $date): bool
    {
        $timestamp = strtotime($date);
        return $timestamp !== false && $timestamp > time();
    }
}