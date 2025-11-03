<?php
namespace App\Support;

class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data);
        
        $viewPath = __DIR__ . '/../../views/' . $template . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$template}");
        }
        
        require $viewPath;
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function url(string $path): string
    {
        $config = require __DIR__ . '/../../config/app.php';
        return rtrim($config['app_url'], '/') . '/' . ltrim($path, '/');
    }

    public static function asset(string $path): string
    {
        return self::url('assets/' . ltrim($path, '/'));
    }
}