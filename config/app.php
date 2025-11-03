<?php
return [
    'app_url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'session_name' => $_ENV['SESSION_NAME'] ?? 'shorturl_session',
    'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? 'csrf_token',
];