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
    <link rel="stylesheet" href="/assets/css/admindashboard.css">    
    <title>Admin Dashboard</title>
</head>
<body>
    <?php if ($success): ?>
                    <div class="alert alert-success mb-3" id="submitError">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger mb-3" id="submitError">
                        <?= htmlspecialchars($error) ?>
                    </div>
    <?php endif; ?>
    <div style="display: flex; height: 100vh;">
        
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require __DIR__ . '/../layouts/navbar.php'; ?>
            
            <div class="dashboard-container" style="flex: 1; overflow-y: auto; padding: 30px;">
                <div style="margin-bottom: 30px;">
                    <h3>Welcome <?= $user['name'] ?> </h3>
                </div>
                <div>
                    <div class="table-responsive">
                        <h3 class="p-3">Latest Users</h3>
                        <table border="1" cellpadding="8" cellspacing="0" class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= ($user['name']) ?></td>
                                        <td><?= ($user['email']) ?></td>
                                        <td><?= ($user['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>