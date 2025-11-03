<?php
// Admin user seeding script
// Usage: php scripts/seed_admin.php [username] [password]

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

// Database configuration
require __DIR__ . '/../config/database.php';

// Get username and password from command line arguments or prompt
$username = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$username) {
    echo "Enter admin username: ";
    $username = trim(fgets(STDIN));
}

if (!$password) {
    echo "Enter admin password: ";
    $password = trim(fgets(STDIN));
}

if (empty($username) || empty($password)) {
    echo "Username and password are required.\n";
    echo "Usage: php scripts/seed_admin.php [username] [password]\n";
    exit(1);
}

try {
    // Connect to database
    $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "User '{$username}' already exists. Update password? (y/n): ";
        $confirm = trim(fgets(STDIN));
        
        if (strtolower($confirm) !== 'y') {
            echo "Operation cancelled.\n";
            exit(0);
        }
        
        // Update existing user
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
        $stmt->execute([$passwordHash, $username]);
        
        echo "Password updated for user '{$username}'.\n";
    } else {
        // Create new user
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$username, $passwordHash]);
        
        echo "Admin user '{$username}' created successfully.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}