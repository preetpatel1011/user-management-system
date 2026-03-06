<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <title>Document</title>
</head>
<body>
   

   <nav class="navbar navbar-dark bg-primary p-3 shadow-sm rounded-bottom d-flex justify-content-space-between">
        <div class="container-fluid">
            <div class="text-white"><?php echo $siteSettings['website_name']?></div>
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle fw-semibold shadow-sm" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false"> Menu </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="/profile/edit"> Update Profile</a></li>
                        <li><a class="dropdown-item" href="/change-password"> Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="/logout" method="POST" class="mb-0" style="display: inline;">
                            <button type="submit" class="dropdown-item text-danger"> Logout </button>
                        </form></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container d-flex justify-content-center">
        <div class="card p-5 mt-5 shadow rounded-4" style="max-width: 420px; width: 100%;">
             <div class="mb-2 text-center">
                    <img src="<?= !empty($profile['avatar']) 
                    ? '/avatar.php?file=' . htmlspecialchars($profile['avatar']) 
                    : '/assets/images/admin/default_avatar.jpg' ?>" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #0d6efd;">
                </div>
            <h4 class="mb-4 text-primary text-center"><u>User Details</u></h4>
            <div class="mb-3">
                <span class="fw-bold">Name:</span> <span class="text-dark"><?= $user['name'] ?></span>
            </div>
            <div class="mb-3">
                <span class="fw-bold">Email:</span> <span class="text-dark"><?= $user['email'] ?></span>
            </div>
            <?php if (!empty($profile['bio'])): ?>
                <div class="mb-3">
                    <span class="fw-bold">Bio:</span>
                    <p class="text-dark mt-2  rounded"><?= (htmlspecialchars($profile['bio'])) ?></p>
                </div>
            <?php endif; ?>
            <div class="mb-3">
                <span class="fw-bold ">Account: <?= $user['status'] ? "Active" : "Deactive" ?></span>
            </div>
        </div>
    </div>

    <script src="/public/assets/js/validate.js"></script>
</body>
</html>
