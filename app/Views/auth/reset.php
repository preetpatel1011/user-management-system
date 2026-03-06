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
    <title>Document</title>
</head>
<body>
    <div class="container d-flex justify-content-center mt-5">
        <div class="card mt-5 p-5 w-50">
            <form method="POST" action="/reset" id="form">
                <h2>Forgot Password</h2>
                <?php if ($success): ?>
                    <div class="alert alert-success mb-3">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger mb-3" id="submitError">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                Email : <input type="text" id="email" name="email" class="form-control">
                <div>
                    <button name="login" class="btn btn-primary mt-3 mb-3">Send Mail</button>
                    <a href="/login">Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>