<?php  
$user = $user ?? [];
$error = $error ?? null;
$success = $success ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Change Password</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary p-3 shadow-sm rounded-bottom d-flex justify-content-space-between">
        <div class="container-fluid">
            <div class="text-white">Change Password</div>
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle fw-semibold shadow-sm" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="/dashboard">
                            Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="/profile/edit">
                            Update Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="/logout" method="POST" class="mb-0" style="display: inline;">
                            <button type="submit" class="dropdown-item text-danger">
                                Logout
                            </button>
                        </form></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container d-flex justify-content-center">
        <div class="card p-5 mt-5 shadow rounded-4" style="max-width: 420px; width: 100%;">
            <h4 class="mb-4 text-primary text-center"><u>Change Password</u></h4>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="/change-password" method="POST">
                <div class="mb-3">
                    <label for="current_password" class="form-label fw-bold">Current Password </label> 
                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter your current password"required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label fw-bold">New Password </label> 
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter your new password"required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label fw-bold">Confirm Password </label> 
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password"required>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                    <a href="/dashboard" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/public/assets/js/validate.js"></script>
</body>
</html>
