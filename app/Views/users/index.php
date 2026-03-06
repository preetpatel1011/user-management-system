<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Welcome</title>
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>
    <div class="welcome-container">
        <h1 class="welcome-title">Welcome Back! <?php  echo $user['name'] ?></h1>
        <p class="welcome-subtitle">
            Access your dashboard to view and update your information.
        </p>

        <div class="button-container">
            <a href="/dashboard" class="cta-button">
                <i class="fas fa-arrow-right"></i>
                Go to Dashboard
            </a>
            <a href="/profile/edit" class="secondary-button">
                <i class="fas fa-edit"></i>
                Edit Profile
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
