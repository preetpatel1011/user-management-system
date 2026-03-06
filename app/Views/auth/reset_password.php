<?php
    $error = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);

    $success = $_SESSION['success'] ?? null;
    unset($_SESSION['success']);
    
    $old = $_SESSION['old'] ?? [];  
    unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/Auth/login.css">
    
    <title>Login</title>
</head>
<body>
    <div class="container d-flex justify-content-center mt-5">
        <div class="card mt-5 p-5 w-50">
            <form method="POST" action="/reset-password?token=<?= urlencode($token ?? '') ?>" id="form">
                <h2>Reset Password</h2>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success mb-3">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger mb-3" id="submitError">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                <?php endif; ?>
                <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter new password">                
                <label for="password_confirm" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="6"placeholder="Confirm password">                
                    <div>
                    <button name="login" class="btn btn-primary mt-3 mb-3" onclick="onLogin()">Change</button>
                    <a href="/register">New User? | Register Here</a>
                    <a href="/reset">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>