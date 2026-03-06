<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/adminusersedit.css">
    <title>Document</title>
</head>
<body>
    <div style="display: flex; height: 100vh;">
        
        <?php require __DIR__ . '/../../layouts/sidebar.php'; ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require __DIR__ . '/../../layouts/navbar.php'; ?>
            
            <div style="flex: 1; overflow-y: auto; padding: 30px;">
    <div class="page-body">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Edit User Profile</h2>
                    <p>Update user details, verification status and password</p>
                </div>
            </div>

            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" id="editUserError" style="margin-bottom:10px;">
                        <?= ($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/admin/user/update" id="form">
                    <input type="hidden" name="id" value="<?= ($user['id'] ?? '') ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   value="<?= ($user['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                   value="<?= ($user['email'] ?? '') ?>" required>
                        </div>

                        <div class="form-group full">
                            <label for="is_email_verified">Email Verification</label>
                            <select name="is_email_verified" id="is_email_verified" class="form-control">
                                <option value="1" <?= (isset($user['is_email_verified']) && $user['is_email_verified'] ? 'selected' : '') ?>>Verified</option>
                                <option value="0" <?= (!isset($user['is_email_verified']) || !$user['is_email_verified'] ? 'selected' : '') ?>>Not Verified</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-label">Change Password</div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control"
                                   placeholder="Leave blank to keep current">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                   placeholder="Re-enter new password">
                        </div>
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="btn btn-primary" onclick="onEditUser()">Update User</button>
                        <div class="nav-links">
                            <a href="/admin/dashboard">Dashboard</a>
                            <a href="/admin/users">← All Users</a>
                        </div>
                    </div>
                </form>

                <div id="editUserError" style="color:red;margin-top:10px;"></div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
    <script src="/assets/js/validate.js"></script>
</body>
</html>