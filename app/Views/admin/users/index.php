<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require __DIR__ . '/../../layouts/header.php'; ?>
    <link rel="stylesheet" href="/assets/css/adminusers.css">
    <title>All Users</title>
</head>
<body>
    <div style="display: flex; height: 100vh;">
        
        <?php require __DIR__ . '/../../layouts/sidebar.php'; ?>
        <div style="flex: 1; display: flex; flex-direction: column;">
            <?php require __DIR__ . '/../../layouts/navbar.php'; ?>
            
            <div style="flex: 1; overflow-y: auto; padding: 30px;">
                <h2>All Users</h2>
                
                <div class="mb-4">
                    <form method="GET" action="/admin/users" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email"
                            value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                        <button type="submit" class="btn btn-secondary">Search</button>
                        <?php if ($searchQuery): ?>
                            <a href="/admin/users" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-responsive">
                    <table border="1" cellpadding="8" cellspacing="0" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Approved Status</th>
                                <th scope="col">Active Status</th>
                                <th scope = 'col'>Email Verify Date</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
        
                                    <td scope="row"><?= ($user['name']) ?></td>
                                    <td><?= ($user['email']) ?></td>
                                    <td>
                                        <?php
                                            if ($user['status'] == 1) echo '<span class="badge bg-success">Approved</span>';
                                            elseif ($user['status'] == 0) echo '<span class="badge bg-danger">Rejected</span>';
                                        ?>
                                        <?php if ($user['status'] !== 0 && $user['status'] !== 1): ?>
                                            <div class="mt-2">
                                                <form method="POST" action="/admin/user/approved-status" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                                <form method="POST" action="/admin/user/approved-status" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="status" value="0">
                                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            // if ($user['is_active'] == 1) echo '<span class="badge bg-success">active</span>';
                                            // elseif ($user['is_active'] == 0) echo '<span class="badge bg-danger">deactive</span>';
                                        ?>
                                        <?php if ($user['is_active'] == 0): ?>
                                            <div class="mt-2">
                                                <form method="POST" action="/admin/user/active-status" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="is_active" value="1">
                                                    <button type="submit" class="btn btn-success btn-sm">Activate</button>
                                                </form>
                                            </div>
                                        <?php elseif ($user['is_active'] == 1): ?>
                                            <div class="mt-2">
                                                <form method="POST" action="/admin/user/active-status" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="is_active" value="0">
                                                    <button type="submit" class="btn btn-danger btn-sm">Deactivate</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $user['email_verified_at']?? '--' ?></td>
                                    <td><?= ($user['created_at']) ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="userAction<?= $user['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="userAction<?= $user['id'] ?>">
                                               <li>
                                                   <a href="/admin/user/edit?id=<?= $user['id'] ?>" class="dropdown-item">
                                                       <i class="bi bi-pencil"></i> Edit
                                                   </a>
                                               </li> 
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form method="POST" action="/admin/user/delete" style="display:inline;" onsubmit="return confirm('Are you sure to delete this user?');">
                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href=""></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php
                        if (empty($users))
                            echo '<span class = "not-found">user not found</span>'
                    ?>
                </div>

                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                            <a class="page-link" href="/admin/users?page=<?= max(1, $currentPage - 1) ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>">Previous</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="/admin/users?page=<?= min($totalPages, $currentPage + 1) ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>">Next</a>
                        </li>
                    </ul>
                </nav>

                <div class="text-center mt-2 text-muted">
                    Showing <?= count($users) > 0 ? (($currentPage - 1) * 5 + 1) : 0 ?> to <?= ($currentPage - 1) * 5 + count($users) ?>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../../layouts/footer.php'; ?>
</body>
</html>