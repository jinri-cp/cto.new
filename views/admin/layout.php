<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Short URL Admin' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .table-actions {
            white-space: nowrap;
        }
        .search-form {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= View::url('admin/urls') ?>">Short URL Admin</a>
            
            <?php if (isset($user) && $user): ?>
                <div class="navbar-nav ms-auto">
                    <span class="navbar-text me-3">Welcome, <?= View::escape($user['username']) ?></span>
                    <form method="POST" action="<?= View::url('admin/logout') ?>" class="d-inline">
                        <?= $csrf_field ?? '' ?>
                        <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container mt-4">
        <?php
        $error = Session::getFlash('error');
        $success = Session::getFlash('success');
        
        if ($error):
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>