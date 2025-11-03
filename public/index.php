<?php
// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Simple router
$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = parse_url($request_uri, PHP_URL_PATH);
$request_uri = rtrim($request_uri, '/');

// Start session
\App\Support\Session::start();

// Routes
if ($request_uri === '' || $request_uri === '/') {
    // Home page - simple redirect to admin
    header('Location: /admin/urls');
    exit;
}

// Short URL redirect
if (!str_starts_with($request_uri, '/admin')) {
    $code = ltrim($request_uri, '/');
    if (!empty($code)) {
        $controller = new \App\Controllers\RedirectController();
        $controller->go($code);
    } else {
        header('Location: /admin/urls');
        exit;
    }
}

// Admin routes
if (str_starts_with($request_uri, '/admin')) {
    if ($request_uri === '/admin/login') {
        $controller = new \App\Controllers\AuthController();
        $controller->login();
    } elseif ($request_uri === '/admin/logout') {
        $controller = new \App\Controllers\AuthController();
        $controller->logout();
    } elseif ($request_uri === '/admin/urls') {
        $controller = new \App\Controllers\UrlController();
        $controller->index();
    } elseif ($request_uri === '/admin/urls/create') {
        $controller = new \App\Controllers\UrlController();
        $controller->create();
    } elseif (preg_match('#^/admin/urls/(\d+)/edit$#', $request_uri, $matches)) {
        $controller = new \App\Controllers\UrlController();
        $controller->edit((int) $matches[1]);
    } elseif (preg_match('#^/admin/urls/(\d+)/delete$#', $request_uri, $matches)) {
        $controller = new \App\Controllers\UrlController();
        $controller->delete((int) $matches[1]);
    } elseif ($request_uri === '/admin') {
        header('Location: /admin/urls');
        exit;
    } else {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
    }
} else {
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
}
?>