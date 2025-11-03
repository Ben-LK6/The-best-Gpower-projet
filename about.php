<?php 
$page_title = "GPower - À Propos";
$css_file = "about.css";
include 'includes/header.php'; 

include 'includes/tracking.php';
trackPageView('about'); 
?>

<div class="page-simple-header">
    <div class="container">
        <h1>Notre Expertise</h1>
    </div>
</div>

<section class="about-simple">
    <div class="container">
        <!-- Introduction -->
        <div class="about-intro">
            <h2>GPower - L'excellence de l'outil</h2>
            <p>Spécialiste des équipements professionnels, nous fournissons aux artisans et industries des outils qui allient performance, durabilité et précision.</p>
        </div>

        <!-- Notre Histoire -->
        <div class="section-block">
            <h3>Notre Histoire</h3>
            <p>Née de la passion pour l'artisanat, GPower s'est imposée comme le partenaire de confiance des professionnels exigeants. Notre mission : équiper l'excellence.</p>
        </div>

        <!-- Notre Mission -->
        <div class="section-block">
            <h3>Notre Mission</h3>
            <p>Fournir des outils qui transforment le travail en œuvre de précision. Chaque produit est sélectionné pour sa performance et sa fiabilité.</p>
        </div>

        <!-- Valeurs et Spécialités côte à côte -->
        <div class="two-columns">
            <!-- Colonne Valeurs -->
            <div class="column">
                <h3>Nos Valeurs</h3>
                <div class="values-simple">
                    <div class="value-line">
                        <span class="value-icon">✓</span>
                        <span class="value-text"><strong>Qualité</strong> - Produits testés et approuvés</span>
                    </div>
                    <div class="value-line">
                        <span class="value-icon">✓</span>
                        <span class="value-text"><strong>Performance</strong> - Outils haute performance</span>
                    </div>
                    <div class="value-line">
                        <span class="value-icon">✓</span>
                        <span class="value-text"><strong>Fiabilité</strong> - Durabilité exceptionnelle</span>
                    </div>
                    <div class="value-line">
                        <span class="value-icon">✓</span>
                        <span class="value-text"><strong>Service</strong> - Accompagnement personnalisé</span>
                    </div>
                </div>
            </div>

            <!-- Colonne Spécialités -->
            <div class="column">
                <h3>Notre Savoir-Faire</h3>
                <div class="expertise-simple">
                    <div class="expertise-line">
                        <span class="expertise-icon">🛠️</span>
                        <span class="expertise-text">Outillage professionnel</span>
                    </div>
                    <div class="expertise-line">
                        <span class="expertise-icon">🔧</span>
                        <span class="expertise-text">Équipements spécialisés</span>
                    </div>
                    <div class="expertise-line">
                        <span class="expertise-icon">⚙️</span>
                        <span class="expertise-text">Solutions techniques</span>
                    </div>
                    <div class="expertise-line">
                        <span class="expertise-icon">🎯</span>
                        <span class="expertise-text">Conseil expert</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement final -->
        <div class="commitment">
            <h3>Notre Engagement</h3>
            <p>Chez GPower, nous ne vendons pas simplement des outils - nous fournissons des solutions qui améliorent votre productivité et garantissent votre succès.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>