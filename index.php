<?php 
include 'includes/tracking.php';
trackPageView('home');

$page_title = "GPower - Accueil";
$css_file = "index.css";
include 'includes/header.php'; 
?>
<section class="hero">
    <div class="hero-content">
       
        <h1>L'Excellence <span class="highlight">Professionnelle</span></h1>
        <p class="hero-subtitle">Des outils d'exception pour des résultats remarquables ⭐ </p>

        <div class="hero-buttons">
            <a href="products.php" class="btn btn-primary">
                <span class="btn-icon">🔍</span>
                Découvrir nos produits 
            </a>
        </div>
    </div>
    <div class="hero-scroll">
        <span>Explorer</span>
        <div class="scroll-arrow">↓</div>
    </div>
  

</section>
<?php include 'includes/footer.php'; ?>