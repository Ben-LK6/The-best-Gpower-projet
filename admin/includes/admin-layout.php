<?php
// Vérifier l'authentification
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Variables par défaut
$page_title = $page_title ?? 'GPower Admin';
$current_page = $current_page ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="css/admin-dashboard.css">
    <link rel="stylesheet" href="css/admin-forms.css">
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
                <a href="dashboard.php" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="products.php" class="nav-item <?php echo $current_page === 'products' ? 'active' : ''; ?>">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Products</span>
                </a>
                <a href="categories.php" class="nav-item <?php echo $current_page === 'categories' ? 'active' : ''; ?>">
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
                    <div class="breadcrumb">
                        <?php if(isset($breadcrumb)): ?>
                            <?php foreach($breadcrumb as $item): ?>
                                <?php if(isset($item['url'])): ?>
                                    <a href="<?php echo $item['url']; ?>" class="breadcrumb-item"><?php echo $item['title']; ?></a>
                                    <span class="breadcrumb-separator">›</span>
                                <?php else: ?>
                                    <span class="breadcrumb-item current"><?php echo $item['title']; ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-avatar">👤</span>
                        <span class="user-name">Admin</span>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="page-content">
                <?php echo $content ?? ''; ?>
            </div>
        </main>
    </div>
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <!-- Confirmation Modal -->
    <div class="confirm-modal" id="confirmModal">
        <div class="confirm-dialog">
            <div class="confirm-header">
                <div class="confirm-icon">🗑️</div>
                <h3 class="confirm-title" id="confirmTitle">Confirm Deletion</h3>
                <p class="confirm-message" id="confirmMessage">Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>
            <div class="confirm-actions">
                <button class="confirm-btn confirm-btn-cancel" onclick="hideConfirmModal()">Cancel</button>
                <button class="confirm-btn confirm-btn-delete" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
    
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
        
        // Confirmation Modal Functions
        window.showConfirmModal = function(title, message, onConfirm) {
            const modal = document.getElementById('confirmModal');
            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const deleteBtn = document.getElementById('confirmDeleteBtn');
            
            titleEl.textContent = title;
            messageEl.textContent = message;
            
            // Remove previous event listeners
            const newDeleteBtn = deleteBtn.cloneNode(true);
            deleteBtn.parentNode.replaceChild(newDeleteBtn, deleteBtn);
            
            // Add new event listener
            newDeleteBtn.addEventListener('click', function() {
                hideConfirmModal();
                onConfirm();
            });
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        };
        
        window.hideConfirmModal = function() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        };
        
        // Close modal on overlay click
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideConfirmModal();
            }
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('confirmModal').classList.contains('show')) {
                hideConfirmModal();
            }
        });
    </script>
</body>
</html>