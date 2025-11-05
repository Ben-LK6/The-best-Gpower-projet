<?php 
require_once 'includes/language.php';
$page_title = t('about_title');
$css_file = "about.css";
include 'includes/header.php'; 

include 'includes/tracking.php';
trackPageView('about'); 
?>

<div class="page-simple-header">
    <div class="container">
        <h1><?php echo t('about_header'); ?></h1>
    </div>
</div>

<section class="about-simple">
    <div class="container">
        <!-- Introduction -->
        <div class="about-intro">
            <h2><?php echo t('about_intro_title'); ?></h2>
            <p><?php echo t('about_intro_text'); ?></p>
        </div>

        <!-- Notre Histoire -->
        <div class="section-block">
            <h3><?php echo t('about_history'); ?></h3>
            <p><?php echo t('about_history_text'); ?></p>
        </div>

        <!-- Notre Mission -->
        <div class="section-block">
            <h3><?php echo t('about_mission'); ?></h3>
            <p><?php echo t('about_mission_text'); ?></p>
        </div>

        <!-- Valeurs et Spécialités côte à côte -->
        <div class="two-columns">
            <!-- Colonne Valeurs -->
            <div class="column">
                <h3><?php echo t('about_values'); ?></h3>
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
                <h3><?php echo t('about_expertise'); ?></h3>
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
            <h3><?php echo t('about_commitment'); ?></h3>
            <p><?php echo t('about_commitment_text'); ?></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>