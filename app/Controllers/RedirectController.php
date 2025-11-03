<?php
namespace App\Controllers;

use App\Models\ShortUrl;
use App\Support\View;

class RedirectController
{
    public function go(string $code): void
    {
        $url = ShortUrl::findByCode($code);
        
        if (!$url) {
            http_response_code(404);
            $this->showError('404 - URL Not Found', 'The short URL you requested does not exist.');
            return;
        }
        
        if ($url['is_active'] == 0) {
            http_response_code(404);
            $this->showError('URL Disabled', 'This short URL has been disabled.');
            return;
        }
        
        if ($url['expire_at'] && strtotime($url['expire_at']) < time()) {
            http_response_code(404);
            $this->showError('URL Expired', 'This short URL has expired.');
            return;
        }
        
        header('Location: ' . $url['long_url'], true, 301);
        exit;
    }

    private function showError(string $title, string $message): void
    {
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 1rem;
        }
        p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>' . htmlspecialchars($title) . '</h1>
        <p>' . htmlspecialchars($message) . '</p>
        <a href="/" class="btn">Go Home</a>
    </div>
</body>
</html>';
    }
}