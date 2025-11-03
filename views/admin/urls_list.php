<?php
$title = 'Short URLs';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Short URLs</h2>
    <a href="<?= View::url('admin/urls/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create New
    </a>
</div>

<form method="GET" action="<?= View::url('admin/urls') ?>" class="search-form">
    <div class="row">
        <div class="col-md-8">
            <input type="text" class="form-control" name="search" 
                   placeholder="Search by code or URL..." value="<?= View::escape($search) ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            <a href="<?= View::url('admin/urls') ?>" class="btn btn-outline-secondary">Clear</a>
        </div>
    </div>
</form>

<?php if (empty($urls)): ?>
    <div class="alert alert-info">
        <?php if ($search): ?>
            No short URLs found matching "<?= View::escape($search) ?>".
        <?php else: ?>
            No short URLs found. <a href="<?= View::url('admin/urls/create') ?>">Create your first one!</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Short Code</th>
                    <th>Long URL</th>
                    <th>Status</th>
                    <th>Expires</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($urls as $url): ?>
                    <tr>
                        <td>
                            <code><?= View::escape($url['code']) ?></code>
                            <br>
                            <small class="text-muted"><?= View::url($url['code']) ?></small>
                        </td>
                        <td>
                            <a href="<?= View::escape($url['long_url']) ?>" target="_blank" class="text-truncate d-block" style="max-width: 300px;">
                                <?= View::escape($url['long_url']) ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($url['is_active']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($url['expire_at']): ?>
                                <?= date('M j, Y', strtotime($url['expire_at'])) ?>
                                <?php if (strtotime($url['expire_at']) < time()): ?>
                                    <span class="badge bg-warning">Expired</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Never</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M j, Y', strtotime($url['created_at'])) ?></td>
                        <td class="table-actions">
                            <a href="<?= View::url('admin/urls/' . $url['id'] . '/edit') ?>" 
                               class="btn btn-sm btn-outline-primary">Edit</a>
                            
                            <form method="POST" action="<?= View::url('admin/urls/' . $url['id'] . '/delete') ?>" 
                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this short URL?')">
                                <?= $csrf_field ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination['last_page'] > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>