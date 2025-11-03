<?php
namespace App\Services;

use App\Models\User;
use App\Support\Session;

class AuthService
{
    public function login(string $username, string $password): array
    {
        $errors = [];
        
        if (!Session::get('login_attempts', 0)) {
            Session::set('login_attempts', 0);
        }
        
        if (Session::get('login_attempts') >= 5) {
            $errors[] = 'Too many failed login attempts. Please try again later.';
            return ['success' => false, 'errors' => $errors];
        }
        
        $user = User::findByUsername($username);
        
        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            Session::set('login_attempts', Session::get('login_attempts') + 1);
            $errors[] = 'Invalid username or password';
            return ['success' => false, 'errors' => $errors];
        }
        
        Session::set('login_attempts', 0);
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('logged_in', true);
        
        return ['success' => true];
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function isLoggedIn(): bool
    {
        return Session::get('logged_in', false) === true;
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return User::findById(Session::get('user_id'));
    }
}