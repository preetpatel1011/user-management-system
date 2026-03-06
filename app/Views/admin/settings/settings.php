<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/settings.css">    
    <title>Website Settings</title>
</head>
<body>
    <div style="display: flex; height: 100vh;">
        
        <?php require __DIR__ . '/../../layouts/sidebar.php'; ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require __DIR__ . '/../../layouts/navbar.php'; ?>
            
            <div class="settings-container">
                <h1 class="settings-title">Website Settings</h1>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <div class="settings-card">
                    <form action="/admin/settings/update" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="website_name">Website Name</label>
                                <input type="text" id="website_name" name="website_name" placeholder="Enter website name" value="<?= htmlspecialchars($websiteName ?? '') ?>">
                            </div>
                        </div>
    
                        <div style="margin-top: 25px;">
                            <button type="submit" class="submit-btn">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../../layouts/footer.php'; ?>
</body>
</html>
