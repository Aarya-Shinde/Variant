<?php
// Get current page filename for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="../style/admin_dash.css">
<div class="sidebar">
    <div class="logo-text">
        <a href="../index.php">
            VARIANT<span class="logo-dot">.</span>
        </a>
        <small>ADMIN</small>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/variant/pages/admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">
                    <i class="nav-icon"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="/variant/pages/admin/manage_novels.php" class="<?= $current_page == 'manage_novels.php' ? 'active' : '' ?>">
                    <i class="nav-icon"></i> Manage Novels
                </a>
            </li>
            <li>
                <a href="/variant/pages/admin/all_novels.php" class="<?= $current_page == 'all_novels.php' ? 'active' : '' ?>">
                    <i class="nav-icon"></i> All Novels
                </a>
            </li>
            <li>
                <a href="/variant/pages/admin/users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
                    <i class="nav-icon"></i> Users
                </a>
            </li>
            <li>
                <a href="/variant/pages/admin/reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>">
                    <i class="nav-icon"></i> Reports
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-link">
            <span>Logout</span>
        </a>
    </div>
</div>