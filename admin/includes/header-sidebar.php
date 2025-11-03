<?php
// header-sidebar.php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower Admin - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-layout">
        <!-- Menu Hamburger Mobile -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Overlay pour mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">⚡</div>
                    <div class="logo-text">
                        <span class="logo-main">GPower</span>
                        <span class="logo-sub">Admin</span>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                
                <a href="products.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Produits</span>
                </a>
                
                <a href="categories.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                    <span class="nav-icon">📁</span>
                    <span class="nav-text">Catégories</span>
                </a>
                
                <a href="../index.php" class="nav-item" target="_blank">
                    <span class="nav-icon">👁️</span>
                    <span class="nav-text">Voir le Site</span>
                </a>
                
                <a href="../products.php" class="nav-item" target="_blank">
                    <span class="nav-icon">🛍️</span>
                    <span class="nav-text">Boutique</span>
                </a>
                
                <a href="logout.php" class="nav-item logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Déconnexion</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-content">