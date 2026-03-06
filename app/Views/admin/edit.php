<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/editadminprofile.css">
    <title>Document</title>
</head>
<body>
    <div style="display: flex; height: 100vh;">
        
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require __DIR__ . '/../layouts/navbar.php'; ?>
            
            <div style="flex: 1; overflow-y: auto; padding: 30px;">
    <div class="page-body">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2>Update Profile</h2>
                    <p>Update your account name and email</p>
                </div>
            </div>

            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-3">
                        <?= ($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/admin/edit-profile" id="form">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="/admin/dashboard" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>