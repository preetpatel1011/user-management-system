<?php
function isActive($route) {
    return strpos($_SERVER['REQUEST_URI'], $route) !== false;
}

?>
<div class="col-md-3 col-lg-2 admin-sidebar" style="width: 220px; min-width: 220px; max-width: 220px; min-height: calc(100vh - 62px); flex-shrink: 0; background: #1a1f2e; padding: 20px 12px; display: flex; flex-direction: column; font-family: 'Sora', 'Segoe UI', sans-serif;">

    <p style="color: rgba(255,255,255,0.35); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin: 0 0 12px 8px; padding: 0;">Admin Panel</p>

    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 2px;">
        <li>
            <a href="/admin/dashboard"
               class="sidebar-link <?= isActive('/admin/dashboard') && !isActive('/admin/users') && !isActive('/admin/admin-users') ? 'active' : '' ?>"
               style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; font-size: 13px; font-weight: 400; color: rgba(255,255,255,0.6); text-decoration: none; border-left: 3px solid transparent; transition: all 0.15s; white-space: nowrap; line-height: 1;">
                <img src="/assets/images/admin/dashboard.png" width="16" height="16" alt="" style="opacity:0.6; flex-shrink:0;">
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="/admin/users"
               class="sidebar-link <?= isActive('/admin/users') && !isActive('/admin/admin-users') ? 'active' : '' ?>"
               style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; font-size: 13px; font-weight: 400; color: rgba(255,255,255,0.6); text-decoration: none; border-left: 3px solid transparent; transition: all 0.15s; white-space: nowrap; line-height: 1;">
                <img src="/assets/images/admin/customer.png" width="16" height="16" alt="" style="opacity:0.6; flex-shrink:0;">
                <span>Users</span>
            </a>
        </li>
        <li>
            <a href="/admin/admin-users"
               class="sidebar-link <?= isActive('/admin/admin-users') ? 'active' : '' ?>"
               style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; font-size: 13px; font-weight: 400; color: rgba(255,255,255,0.6); text-decoration: none; border-left: 3px solid transparent; transition: all 0.15s; white-space: nowrap; line-height: 1;">
                <img src="/assets/images/admin/administrator.png" width="16" height="16" alt="" style="opacity:0.6; flex-shrink:0;">
                <span>Admins</span>
            </a>
        </li>
    </ul>

    <div style="margin-top: auto; padding-top: 20px; display: flex; flex-direction: column; gap: 8px;">
        <form action="/admin/settings">
            <button type="submit" class="sidebar-btn" style="width: 100%; background: transparent; border: 1px solid rgba(255,255,255,0.18); color: rgba(255,255,255,0.6); border-radius: 8px; padding: 9px 12px; font-size: 13px; cursor: pointer; transition: all 0.15s;">
                Settings
            </button>
        </form>
        <form action="/logout" method="POST">
            <button type="submit" class="sidebar-btn" style="width: 100%; background: transparent; border: 1px solid rgba(255,255,255,0.18); color: rgba(255,255,255,0.6); border-radius: 8px; padding: 9px 12px; font-size: 13px; cursor: pointer; transition: all 0.15s;">
                Logout
            </button>
        </form>
    </div>
</div>

<style>
    .sidebar-link:hover {
        background: rgba(255,255,255,0.07) !important;
        color: rgba(255,255,255,0.9) !important;
        border-left-color: rgba(108,99,255,0.5) !important;
    }
    .sidebar-link.active {
        background: rgba(108,99,255,0.18) !important;
        color: #a89fff !important;
        border-left-color: #6c63ff !important;
        font-weight: 600 !important;
    }
    .sidebar-btn:hover {
        background: rgba(255,255,255,0.07) !important;
        color: #fff !important;
        border-color: rgba(255,255,255,0.3) !important;
    }
</style>