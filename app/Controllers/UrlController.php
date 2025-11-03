<?php
namespace App\Controllers;

use App\Models\ShortUrl;
use App\Services\AuthService;
use App\Services\UrlService;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\View;

class UrlController
{
    private AuthService $authService;
    private UrlService $urlService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->urlService = new UrlService();
    }

    public function index(): void
    {
        $this->authService->requireAuth();

        $page = (int) ($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');

        $result = ShortUrl::getPaginated($page, 10, $search);

        View::render('admin/urls_list', [
            'urls' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'csrf_field' => Csrf::getHiddenField(),
            'user' => $this->authService->getCurrentUser()
        ]);
    }

    public function create(): void
    {
        $this->authService->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';

            if (!Csrf::validateToken($csrfToken)) {
                Session::flash('error', 'Invalid CSRF token');
                View::render('admin/url_form', [
                    'csrf_field' => Csrf::getHiddenField(),
                    'data' => $_POST,
                    'is_edit' => false
                ]);
                return;
            }

            $result = $this->urlService->createUrl($_POST);

            if ($result['success']) {
                $shortUrl = View::url($result['code']);
                Session::flash('success', "Short URL created successfully: <a href='{$shortUrl}' target='_blank'>{$shortUrl}</a>");
                header('Location: /admin/urls');
                exit;
            } else {
                Session::flash('error', implode('<br>', $result['errors']));
                View::render('admin/url_form', [
                    'csrf_field' => Csrf::getHiddenField(),
                    'data' => $_POST,
                    'is_edit' => false
                ]);
            }
        } else {
            View::render('admin/url_form', [
                'csrf_field' => Csrf::getHiddenField(),
                'data' => [],
                'is_edit' => false
            ]);
        }
    }

    public function edit(int $id): void
    {
        $this->authService->requireAuth();

        $url = ShortUrl::findById($id);
        if (!$url) {
            Session::flash('error', 'Short URL not found');
            header('Location: /admin/urls');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';

            if (!Csrf::validateToken($csrfToken)) {
                Session::flash('error', 'Invalid CSRF token');
                View::render('admin/url_form', [
                    'csrf_field' => Csrf::getHiddenField(),
                    'data' => array_merge($url, $_POST),
                    'is_edit' => true,
                    'id' => $id
                ]);
                return;
            }

            $result = $this->urlService->updateUrl($id, $_POST);

            if ($result['success']) {
                Session::flash('success', 'Short URL updated successfully');
                header('Location: /admin/urls');
                exit;
            } else {
                Session::flash('error', implode('<br>', $result['errors']));
                View::render('admin/url_form', [
                    'csrf_field' => Csrf::getHiddenField(),
                    'data' => array_merge($url, $_POST),
                    'is_edit' => true,
                    'id' => $id
                ]);
            }
        } else {
            View::render('admin/url_form', [
                'csrf_field' => Csrf::getHiddenField(),
                'data' => $url,
                'is_edit' => true,
                'id' => $id
            ]);
        }
    }

    public function delete(int $id): void
    {
        $this->authService->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';

            if (!Csrf::validateToken($csrfToken)) {
                Session::flash('error', 'Invalid CSRF token');
                header('Location: /admin/urls');
                exit;
            }

            $result = $this->urlService->deleteUrl($id);

            if ($result['success']) {
                Session::flash('success', 'Short URL deleted successfully');
            } else {
                Session::flash('error', implode('<br>', $result['errors']));
            }
        }

        header('Location: /admin/urls');
        exit;
    }
}