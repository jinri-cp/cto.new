<?php
// Simple syntax check script
// Usage: php test_syntax.php

$directories = [
    'app/Controllers',
    'app/Models', 
    'app/Services',
    'app/Support',
    'scripts',
    'public',
    'config'
];

$errors = [];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            $output = [];
            $return_var = 0;
            exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return_var);
            
            if ($return_var !== 0) {
                $errors[$file] = implode("\n", $output);
            }
        }
    }
}

if (empty($errors)) {
    echo "✅ All PHP files have valid syntax!\n";
} else {
    echo "❌ Syntax errors found:\n\n";
    foreach ($errors as $file => $error) {
        echo "File: $file\n";
        echo "Error: $error\n\n";
    }
    exit(1);
}