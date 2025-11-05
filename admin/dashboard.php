<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Statistiques basiques
$products_count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categories_count = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_views = $db->query("SELECT COUNT(*) FROM page_views")->fetchColumn();
$recent_views = $db->query("SELECT COUNT(*) FROM page_views WHERE viewed_at >= NOW() - INTERVAL 24 HOUR")->fetchColumn();

// Produits récents
$recent_products = $db->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Vues par page
$views_by_page = $db->query("
    SELECT page_type, COUNT(*) as view_count 
    FROM page_views 
    GROUP BY page_type 
    ORDER BY view_count DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower Admin - Dashboard</title>
    <link rel="stylesheet" href="css/admin-dashboard.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="../images/logo-gpower.png" alt="GPower" class="logo-img">
                    <div class="logo-text">
                        <h2>GPower</h2>
                        <span>Admin Panel</span>
                    </div>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="products.php" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Products</span>
                </a>
                <a href="categories.php" class="nav-item">
                    <span class="nav-icon">📁</span>
                    <span class="nav-text">Categories</span>
                </a>
                <div class="nav-divider"></div>
                <a href="../index.php" class="nav-item" target="_blank">
                    <span class="nav-icon">🌐</span>
                    <span class="nav-text">View Site</span>
                </a>
                <a href="logout.php" class="nav-item logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Logout</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div class="header-left">
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-avatar">👤</span>
                        <span class="user-name">Admin</span>
                    </div>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">📊</div>
                        <div class="stat-info">
                            <h3>Total Views</h3>
                            <div class="stat-number"><?php echo number_format($total_views); ?></div>
                            <div class="stat-change positive">+<?php echo $recent_views; ?> today</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon green">📦</div>
                        <div class="stat-info">
                            <h3>Products</h3>
                            <div class="stat-number"><?php echo $products_count; ?></div>
                            <div class="stat-change">Active products</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon purple">📁</div>
                        <div class="stat-info">
                            <h3>Categories</h3>
                            <div class="stat-number"><?php echo $categories_count; ?></div>
                            <div class="stat-change">Product categories</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orange">🎯</div>
                        <div class="stat-info">
                            <h3>Conversion</h3>
                            <div class="stat-number"><?php echo $products_count > 0 ? round(($recent_views / $products_count), 1) : 0; ?>%</div>
                            <div class="stat-change">View rate</div>
                        </div>
                    </div>
                </div>
                
                <!-- Content Grid -->
                <div class="content-grid">
                    <!-- Recent Products -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3>Recent Products</h3>
                            <a href="products.php" class="card-action">View All</a>
                        </div>
                        <div class="card-content">
                            <?php if(empty($recent_products)): ?>
                                <div class="empty-state">
                                    <span class="empty-icon">📦</span>
                                    <p>No products yet</p>
                                    <a href="add_product.php" class="btn btn-primary">Add Product</a>
                                </div>
                            <?php else: ?>
                                <div class="product-list">
                                    <?php foreach($recent_products as $product): ?>
                                        <div class="product-item">
                                            <div class="product-image">
                                                <?php if($product['image_path']): ?>
                                                    <img src="../uploads/<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name_fr']); ?>">
                                                <?php else: ?>
                                                    <div class="no-image">📦</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="product-info">
                                                <h4><?php echo htmlspecialchars($product['name_fr']); ?></h4>
                                                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                                                <p class="product-date"><?php echo date('M d, Y', strtotime($product['created_at'])); ?></p>
                                            </div>
                                            <div class="product-actions">
                                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-icon">✏️</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Page Views -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3>Page Analytics</h3>
                        </div>
                        <div class="card-content">
                            <?php if(empty($views_by_page)): ?>
                                <div class="empty-state">
                                    <span class="empty-icon">📊</span>
                                    <p>No analytics data</p>
                                </div>
                            <?php else: ?>
                                <div class="analytics-list">
                                    <?php 
                                    $page_icons = [
                                        'home' => '🏠',
                                        'products' => '📦',
                                        'product' => '👁️',
                                        'about' => 'ℹ️',
                                        'contact' => '📞'
                                    ];
                                    $page_names = [
                                        'home' => 'Home Page',
                                        'products' => 'Products List',
                                        'product' => 'Product Details',
                                        'about' => 'About Page',
                                        'contact' => 'Contact Page'
                                    ];
                                    foreach($views_by_page as $page): 
                                    ?>
                                        <div class="analytics-item">
                                            <div class="analytics-icon">
                                                <?php echo $page_icons[$page['page_type']] ?? '📄'; ?>
                                            </div>
                                            <div class="analytics-info">
                                                <h4><?php echo $page_names[$page['page_type']] ?? ucfirst($page['page_type']); ?></h4>
                                                <p><?php echo number_format($page['view_count']); ?> views</p>
                                            </div>
                                            <div class="analytics-progress">
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: <?php echo ($page['view_count'] / $total_views) * 100; ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3>Quick Actions</h3>
                    <div class="actions-grid">
                        <a href="add_product.php" class="action-card">
                            <span class="action-icon">➕</span>
                            <span class="action-text">Add Product</span>
                        </a>
                        <a href="add_category.php" class="action-card">
                            <span class="action-icon">📁</span>
                            <span class="action-text">Add Category</span>
                        </a>
                        <a href="../index.php" class="action-card" target="_blank">
                            <span class="action-icon">🌐</span>
                            <span class="action-text">View Site</span>
                        </a>
                        <a href="../products.php" class="action-card" target="_blank">
                            <span class="action-icon">🛍️</span>
                            <span class="action-text">Shop</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <script>
        // Sidebar functionality
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        // Desktop sidebar toggle
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }
        
        // Mobile menu toggle
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                mobileOverlay.classList.toggle('active');
                document.body.classList.toggle('sidebar-open');
            });
        }
        
        // Close mobile menu on overlay click
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                mobileOverlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            });
        }
        
        // Auto-collapse sidebar on small screens
        function handleResize() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('collapsed');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize();
    </script>
</body>
</html>