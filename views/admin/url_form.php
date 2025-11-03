<?php
$title = $is_edit ? 'Edit Short URL' : 'Create Short URL';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <?= $is_edit ? 'Edit Short URL' : 'Create New Short URL' ?>
                </h2>
                
                <form method="POST" action="<?= View::url($is_edit ? 'admin/urls/' . $id . '/edit' : 'admin/urls/create') ?>">
                    <?= $csrf_field ?>
                    
                    <div class="mb-3">
                        <label for="long_url" class="form-label">Long URL *</label>
                        <input type="url" class="form-control" id="long_url" name="long_url" 
                               value="<?= View::escape($data['long_url'] ?? '') ?>" required>
                        <div class="form-text">The full URL to redirect to (must start with http:// or https://)</div>
                    </div>
                    
                    <?php if (!$is_edit): ?>
                        <div class="mb-3">
                            <label for="custom_code" class="form-label">Custom Short Code</label>
                            <input type="text" class="form-control" id="custom_code" name="custom_code" 
                                   value="<?= View::escape($data['custom_code'] ?? '') ?>"
                                   placeholder="Leave blank to auto-generate">
                            <div class="form-text">4-10 characters (letters, numbers, underscore, dash). Leave blank for random code.</div>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Short Code</label>
                            <div class="form-control-plaintext">
                                <code><?= View::escape($data['code']) ?></code>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" <?= (isset($data['is_active']) && $data['is_active'] == 1) ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= (isset($data['is_active']) && $data['is_active'] == 0) ? 'selected' : '' ?>>Disabled</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expire_at" class="form-label">Expiry Date</label>
                                <input type="datetime-local" class="form-control" id="expire_at" name="expire_at" 
                                       value="<?= View::escape($data['expire_at'] ?? '') ?>">
                                <div class="form-text">Leave blank for no expiry</div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($is_edit): ?>
                        <div class="mb-3">
                            <label class="form-label">Created</label>
                            <div class="form-control-plaintext">
                                <?= date('M j, Y H:i:s', strtotime($data['created_at'])) ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Last Updated</label>
                            <div class="form-control-plaintext">
                                <?= date('M j, Y H:i:s', strtotime($data['updated_at'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= View::url('admin/urls') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <?= $is_edit ? 'Update' : 'Create' ?> Short URL
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>