<?php 
include 'includes/tracking.php';
trackPageView('home');

require_once 'includes/language.php';
$page_title = t('home_title');
$css_file = "index.css";
include 'includes/header.php'; 
?>
<section class="hero">
    <div class="hero-content">
       
        <h1><?php echo t('home_hero_title'); ?></h1>
        <p class="hero-subtitle"><?php echo t('home_hero_subtitle'); ?></p>

        <div class="hero-buttons">
            <a href="products.php" class="btn btn-primary">
                <span class="btn-icon">🔍</span>
                <?php echo t('home_discover_btn'); ?>
            </a>
        </div>
    </div>
    <div class="hero-scroll">
        <span><?php echo t('home_explore'); ?></span>
        <div class="scroll-arrow">↓</div>
    </div>
  

</section>
<?php include 'includes/footer.php'; ?>