<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\View;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login(): void
    {
        if ($this->authService->isLoggedIn()) {
            header('Location: /admin/urls');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $csrfToken = $_POST['csrf_token'] ?? '';

            if (!Csrf::validateToken($csrfToken)) {
                Session::flash('error', 'Invalid CSRF token');
                View::render('admin/login', [
                    'csrf_field' => Csrf::getHiddenField(),
                    'username' => $username
                ]);
                return;
            }

            $result = $this->authService->login($username, $password);

            if ($result['success']) {
                header('Location: /admin/urls');
                exit;
            } else {
                Session::flash('error', implode('<br>', $result['errors']));
                View::render('admin/login', [
                    'csrf_field' => Csrf::getHiddenField(),
                    'username' => $username
                ]);
            }
        } else {
            View::render('admin/login', [
                'csrf_field' => Csrf::getHiddenField(),
                'username' => ''
            ]);
        }
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            
            if (Csrf::validateToken($csrfToken)) {
                $this->authService->logout();
            }
        }
        
        header('Location: /admin/login');
        exit;
    }
}