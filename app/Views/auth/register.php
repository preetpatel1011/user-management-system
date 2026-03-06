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
    <link rel="stylesheet" href="/assets/css/Auth/register.css">    
    <title>Document</title>
</head>
<body>
    <div class="container d-flex justify-content-center mt-5">
        <div class="card w-50 p-4">
            <h2>Register</h2>
            <?php if (($success)): ?>
                <div class="alert alert-success" id="submitError" style="margin-bottom:10px;">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger" id="submitSuccess" style="margin-bottom:10px;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="/register" id="form">
                Name: <input type="text" id="name" name="name" class="form-control" value="<?php echo $old['name'] ?? '' ?>"><br>
                Email: <input type="text" id="email" name="email" class="form-control" value="<?php echo $old['email'] ?? '' ?>"><br>
                Password: <input type="password" id="password" name="password" class="form-control"><br>
                Confirm Password: <input type="password" id="confirm_password" name="confirm_password" class="form-control"><br>
                <button type="submit" class="btn btn-primary mb-4">Register</button>
                <a href="/login"> Already registered? | Login</a>
            </form>
            <div id="submitError" style="color:red;margin-top:10px;"></div>
        </div>
    </div>
</body>
</html>