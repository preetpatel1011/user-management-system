<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/admindashboard.css">
    <title>Document</title>
</head>
<body>
    <div style="display: flex; height: 100vh;">
        
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require __DIR__ . '/../layouts/navbar.php'; ?>
            
            <div style="flex: 1; overflow-y: auto; padding: 30px;">
                <h2>Admin Users</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered rounded shadow-sm">
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
                                    <td><span style="color:#495057;"><?= htmlspecialchars($user['name']) ?></span></td>
                                    <td><span style="color:#495057;"><?= htmlspecialchars($user['email']) ?></span></td>
                                    <td><span style="color:#6c757d;"><?= ($user['created_at']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>