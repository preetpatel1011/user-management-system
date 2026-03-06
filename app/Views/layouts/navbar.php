<nav class="navbar navbar-expand-lg navbar-light" style="background: #111520; box-shadow: 0 2px 16px rgba(0,0,0,0.18); height: 62px; display: flex; align-items: center; padding: 0 28px; position: sticky; top: 0; z-index: 200; width: 100%;">
    <div style="display: flex; align-items: center; gap: 12px; color: white;">
            <span style="font-weight: 600; font-size: 16px;"><?= htmlspecialchars($siteSettings['website_name'] ?? 'Admin Dashboard') ?></span>
        </div>
    <div style="display: flex; align-items: center; width: 100%; padding: 0;">

        <div class="dropdown" style="margin-left: auto; position: relative;">
            <button class="btn btn-outline-secondary dropdown-toggle"
                    type="button"
                    id="adminNavMenu"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; padding: 6px 14px; display: flex; align-items: center; gap: 8px; color: #fff; font-size: 13px; cursor: pointer;">
                <img src="/assets/images/admin/person.png" width="30px" height="30px" alt="person-icon">
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminNavMenu">
                <li><a class="dropdown-item" href="/admin/edit-profile">Update Profile</a></li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        Change Password
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="/logout" method="POST" class="m-0">
                        <button class="dropdown-item" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/reset-password" method="POST" id="resetPasswordForm">
                <div class="modal-body">
                    <?php if (!empty($resetPasswordError)): ?>
                        <div class="alert alert-danger mb-2"><?= $resetPasswordError ?></div>
                    <?php endif; ?>
                    <?php if (!empty($resetPasswordSuccess)): ?>
                        <div class="alert alert-success mb-2"><?= $resetPasswordSuccess ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="oldPassword" class="form-label">Old Password</label>
                        <input type="password" class="form-control" id="oldPassword" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                    <div id="resetPasswordError" class="text-danger mb-2" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
