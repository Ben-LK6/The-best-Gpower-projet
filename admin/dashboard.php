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

// Statistiques des vues
$total_views = $db->query("SELECT COUNT(*) FROM page_views")->fetchColumn();
$product_views = $db->query("SELECT COUNT(*) FROM page_views WHERE page_type = 'product'")->fetchColumn();

// Produits les plus vus
$most_viewed_products = $db->query("
    SELECT p.name_fr, p.id, COUNT(pv.id) as view_count 
    FROM products p 
    LEFT JOIN page_views pv ON p.id = pv.product_id 
    GROUP BY p.id, p.name_fr 
    ORDER BY view_count DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Vues par page
$views_by_page = $db->query("
    SELECT page_type, COUNT(*) as view_count 
    FROM page_views 
    GROUP BY page_type 
    ORDER BY view_count DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Vues par pays
$views_by_country = $db->query("
    SELECT country, COUNT(*) as view_count 
    FROM page_views 
    WHERE country IS NOT NULL 
    GROUP BY country 
    ORDER BY view_count DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// Vues des dernières 24h
$recent_views = $db->query("
    SELECT COUNT(*) as views_today 
    FROM page_views 
    WHERE viewed_at >= NOW() - INTERVAL 24 HOUR
")->fetchColumn();

// Vues des 7 derniers jours (pour graphique)
$views_last_7_days = $db->query("
    SELECT 
        DATE(viewed_at) as view_date,
        COUNT(*) as view_count
    FROM page_views 
    WHERE viewed_at >= NOW() - INTERVAL 7 DAY
    GROUP BY DATE(viewed_at)
    ORDER BY view_date
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower - Analytics Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="dashboard.css">
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

        <!-- Sidebar Simple -->
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
                <a href="dashboard.php" class="nav-item active">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                
                <a href="products.php" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Produits</span>
                </a>
                
                <a href="categories.php" class="nav-item">
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
                <!-- Statistiques principales -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👁️</div>
                        <div class="stat-info">
                            <h3>Vues Total</h3>
                            <p class="stat-number"><?php echo $total_views; ?></p>
                            <p class="stat-change">+<?php echo $recent_views; ?> aujourd'hui</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div class="stat-info">
                            <h3>Produits</h3>
                            <p class="stat-number"><?php echo $products_count; ?></p>
                            <p>Produits en ligne</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🛍️</div>
                        <div class="stat-info">
                            <h3>Vues Produits</h3>
                            <p class="stat-number"><?php echo $product_views; ?></p>
                            <p>Consultations produits</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📁</div>
                        <div class="stat-info">
                            <h3>Catégories</h3>
                            <p class="stat-number"><?php echo $categories_count; ?></p>
                            <p>Catégories actives</p>
                        </div>
                    </div>
                </div>


                <!-- Données détaillées -->
                <div class="details-grid">
                    <!-- Vues par page -->
                    <div class="details-card">
                        <div class="card-header">
                            <h3>🌐 Vues par Page</h3>
                        </div>
                        <div class="page-stats">
                            <?php foreach($views_by_page as $page): ?>
                            <div class="page-stat">
                                <div class="page-info">
                                    <span class="page-icon">
                                        <?php 
                                        $page_icons = [
                                            'home' => '🏠',
                                            'products' => '📦',
                                            'product' => '👁️',
                                            'about' => 'ℹ️',
                                            'contact' => '📞'
                                        ];
                                        echo $page_icons[$page['page_type']] ?? '📄';
                                        ?>
                                    </span>
                                    <span class="page-name">
                                        <?php 
                                        $page_names = [
                                            'home' => 'Accueil',
                                            'products' => 'Liste produits',
                                            'product' => 'Détail produit',
                                            'about' => 'À propos',
                                            'contact' => 'Contact'
                                        ];
                                        echo $page_names[$page['page_type']] ?? $page['page_type'];
                                        ?>
                                    </span>
                                </div>
                                <span class="page-count"><?php echo $page['view_count']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div> <br>

                    <!-- Vues par pays -->
                    <div class="details-card">
                        <div class="card-header"> <br>
                            <h3>🌍 Vues par Pays</h3>
                        </div>
                        <div class="country-stats">
                            <?php if(empty($views_by_country)): ?>
                                <div class="no-data">
                                    <span>🌐</span>
                                    <p>Aucune donnée géographique</p>
                                </div>
                            <?php else: ?>
                                <?php foreach($views_by_country as $country): ?>
                                <div class="country-stat">
                                    <div class="country-info">
                                        <span class="country-flag">🏴</span>
                                        <span class="country-name"><?php echo $country['country'] ?: 'Inconnu'; ?></span>
                                    </div>
                                    <span class="country-count"><?php echo $country['view_count']; ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
    // Menu hamburger mobile
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (mobileMenuToggle && adminSidebar && sidebarOverlay) {
        mobileMenuToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('sidebar-open');
            sidebarOverlay.classList.toggle('active');
        });

        sidebarOverlay.addEventListener('click', function() {
            adminSidebar.classList.remove('sidebar-open');
            sidebarOverlay.classList.remove('active');
        });
    }

    // Graphique des vues
    const viewsCtx = document.getElementById('viewsChart');
    if (viewsCtx) {
        const viewsChart = new Chart(viewsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    $dates = [];
                    foreach($views_last_7_days as $day) {
                        $dates[] = "'" . date('d/m', strtotime($day['view_date'])) . "'";
                    }
                    echo implode(', ', $dates);
                    ?>
                ],
                datasets: [{
                    label: 'Vues',
                    data: [
                        <?php 
                        $counts = [];
                        foreach($views_last_7_days as $day) {
                            $counts[] = $day['view_count'];
                        }
                        echo implode(', ', $counts);
                        ?>
                    ],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#3498db',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
</body>
</html>