<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure le système de langues
require_once __DIR__ . '/language.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'GPower - Outils Professionnels'; ?></title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/whatsapp.css">
    <link rel="stylesheet" href="css/mobile-sidebar.css">
    <link rel="stylesheet" href="css/<?php echo $css_file ?? 'style.css'; ?>">
    <?php if(isset($additional_css)): ?>
    <link rel="stylesheet" href="css/<?php echo $additional_css; ?>">
    <?php endif; ?>
</head>
<body>
    <header class="main-header">
        <nav class="nav-container">
            <div class="logo">
                <a href="index.php" class="logo-link">
                    <img src="images/logo-gpower.png" alt="GPower Logo" class="logo-image">
                    <span class="logo-text">GPower</span>
                </a>
            </div>
            
            <!-- Desktop Menu -->
            <ul class="nav-menu desktop-menu">
                <li><a href="index.php" class="nav-link"><?php echo t('nav_home'); ?></a></li>
                <li><a href="products.php" class="nav-link"><?php echo t('nav_products'); ?></a></li>
                <li><a href="about.php" class="nav-link"><?php echo t('nav_about'); ?></a></li>
                <li><a href="contact.php" class="nav-link"><?php echo t('nav_contact'); ?></a></li>
                <li><?php include 'includes/language_selector.php'; ?></li>
                <li class="whatsapp-header">
                    <?php 
                    require_once 'includes/whatsapp_helper.php';
                    echo getWhatsAppButton('22940870199', t('whatsapp_hello'), 'large');
                    ?>
                </li>
            </ul>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-label="Menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </nav>
        
        <!-- Mobile Sidebar -->
        <div class="mobile-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="images/logo-gpower.png" alt="GPower Logo">
                    <span>GPower</span>
                </div>
                <button class="sidebar-close">&times;</button>
            </div>
            
            <div class="sidebar-content">
                <ul class="sidebar-menu">
                    <li><a href="index.php" class="sidebar-link">
                        <span class="sidebar-icon">🏠</span>
                        <?php echo t('nav_home'); ?>
                    </a></li>
                    <li><a href="products.php" class="sidebar-link">
                        <span class="sidebar-icon">📦</span>
                        <?php echo t('nav_products'); ?>
                    </a></li>
                    <li><a href="about.php" class="sidebar-link">
                        <span class="sidebar-icon">ℹ️</span>
                        <?php echo t('nav_about'); ?>
                    </a></li>
                    <li><a href="contact.php" class="sidebar-link">
                        <span class="sidebar-icon">📞</span>
                        <?php echo t('nav_contact'); ?>
                    </a></li>
                </ul>
                
                <div class="sidebar-actions">
                    <div class="sidebar-language">
                        <h4>🌍 Language</h4>
                        <?php include 'includes/language_selector.php'; ?>
                    </div>
                    
                    <div class="sidebar-contact">
                        <h4>📱 Contact</h4>
                        <div class="sidebar-contact-buttons">
                            <?php echo getWhatsAppButton('22940870199', t('whatsapp_hello'), 'large'); ?>
                            <?php echo getGmailButton('generatorpower60@gmail.com', t('whatsapp_professional'), 'large'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overlay -->
        <div class="mobile-overlay"></div>
    </header>
    <main>

<script>
// Mobile Sidebar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.mobile-sidebar');
    const overlay = document.querySelector('.mobile-overlay');
    const sidebarClose = document.querySelector('.sidebar-close');
    const body = document.body;
    
    // Open sidebar
    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        mobileToggle.classList.add('active');
        body.classList.add('sidebar-open');
    }
    
    // Close sidebar
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        mobileToggle.classList.remove('active');
        body.classList.remove('sidebar-open');
    }
    
    // Event listeners
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Close on link click
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', closeSidebar);
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
    
    // Language selector in sidebar
    const sidebarLangToggle = document.querySelector('.sidebar-language .lang-toggle');
    const sidebarLangSelector = document.querySelector('.sidebar-language .language-selector');
    
    if (sidebarLangToggle && sidebarLangSelector) {
        sidebarLangToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebarLangSelector.classList.toggle('active');
        });
    }
});
</script>