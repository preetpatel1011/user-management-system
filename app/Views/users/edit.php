<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/useredit.css">
    <title>Document</title>
</head>
<body>
    <?php $error = $error ?? null; ?>
    
    <div class="page-body">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Update Profile</h2>
                    <p>Update your name, email and password</p>
                </div>
            </div>

            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="/profile/update" id="form" enctype="multipart/form-data">

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
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4" placeholder="Tell us about yourself..."><?= ($profile && !empty($profile['bio'])) ? htmlspecialchars($profile['bio']) : '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="avatar">Avatar (Profile Picture)</label>
                        <?php if ($profile && !empty($profile['avatar'])): ?>
                            <div class="mb-2">
                                <img src="/avatar.php?file=<?= htmlspecialchars($profile['avatar']) ?>" 
                                    class="rounded-circle" 
                                    style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #0d6efd;">
                                <p class="text-muted mt-2">Current avatar</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                        <small class="text-muted">Allowed: JPG, PNG, GIF (Max 5MB)</small>
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
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="/dashboard" class="btn btn-primary" >Cancel</a>
                    </div>

                </form>

                <div id="editProfileError" style="color:red;margin-top:10px;"></div>
            </div>
        </div>
    </div>

    <script src="/assets/js/validate.js"></script>
</body>
</html>