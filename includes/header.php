<?php
// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'GPower - Outils Professionnels'; ?></title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/<?php echo $css_file ?? 'style.css'; ?>">
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
            
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link">Accueil</a></li>
                <li><a href="products.php" class="nav-link">Produits</a></li>
                <li><a href="about.php" class="nav-link">À Propos</a></li>
                <li><a href="contact.php" class="nav-link">Contact</a></li>
                <!-- Bouton WhatsApp dans le menu -->
                <li>
                    <a href="https://wa.me/2250700000000" class="whatsapp-header" target="_blank">
                        <span class="whatsapp-icon">📱</span>
                        <span class="whatsapp-number">+22940870199</span>
                    </a>
                </li>
            </ul>

            <button class="mobile-menu-toggle" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </header>
    <main>

<script>
// Script simplifié et garanti
const toggle = document.querySelector('.mobile-menu-toggle');
const menu = document.querySelector('.nav-menu');

if (toggle && menu) {
    toggle.addEventListener('click', () => {
        console.log('Menu cliqué - activation');
        toggle.classList.toggle('active');
        menu.classList.toggle('active');
    });
} else {
    console.log('Éléments manquants:', {toggle, menu});
}
</script>