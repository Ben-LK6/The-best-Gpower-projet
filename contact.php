<?php 
require_once 'includes/language.php';
$page_title = t('contact_title');
$css_file = "contact.css";
$additional_css = "contact-pro.css";
include 'includes/header.php'; 

include 'includes/tracking.php';
trackPageView('contact');

// Récupérer le nom du produit si on vient d'une fiche produit
$product_name = '';
$product_id = '';
if(isset($_GET['product'])) {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, name_fr FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['product']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($product) {
        $product_name = $product['name_fr'];
        $product_id = $product['id'];
    }
}
?>

<div class="page-simple-header">
    <div class="container">
        <h1><?php echo t('contact_header'); ?></h1>
        <p class="header-subtitle"><?php echo t('contact_subtitle'); ?></p>
    </div>
</div>

<section class="contact-professional">
    <div class="container">
        <div class="contact-intro">
            <h2><?php echo t('contact_intro_title'); ?></h2>
            <p><?php echo t('contact_intro_text'); ?></p>
        </div>

        <div class="contact-methods">
            <!-- WhatsApp -->
            <div class="contact-card">
                <div class="contact-header">
                    <div class="contact-icon-large">
                        <?php 
                        require_once 'includes/whatsapp_helper.php';
                        echo getWhatsAppButton('22940870199', 
                            $product_name ? 
                            t('whatsapp_product') . ': ' . $product_name . ' (Ref: GP-' . str_pad($product_id, 4, '0', STR_PAD_LEFT) . ')' : 
                            t('whatsapp_hello'), 
                            'large'
                        );
                        ?>
                    </div>
                    <div class="contact-info">
                        <h3><?php echo t('contact_whatsapp'); ?></h3>
                        <p><?php echo t('contact_whatsapp_desc'); ?></p>
                        <span class="contact-badge"><?php echo t('recommended'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="contact-card">
                <div class="contact-header">
                    <div class="contact-icon-large">
                        <?php 
                        echo getGmailButton('generatorpower60@gmail.com', 
                            $product_name ? 
                            t('whatsapp_product') . ': ' . $product_name . ' - GPower' : 
                            t('whatsapp_professional') . ' - GPower', 
                            'large'
                        );
                        ?>
                    </div>
                    <div class="contact-info">
                        <h3><?php echo t('contact_email'); ?></h3>
                        <p><?php echo t('contact_email_desc'); ?></p>
                        <span class="contact-badge business"><?php echo t('business'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="business-info">
            <h3><?php echo t('contact_business_info'); ?></h3>
            <div class="info-grid">
                <div class="info-card">
                    <h4><?php echo t('contact_global_reach'); ?></h4>
                    <p><?php echo t('contact_global_desc'); ?></p>
                </div>
                <div class="info-card">
                    <h4><?php echo t('contact_hours'); ?></h4>
                    <p><?php echo t('contact_hours_desc'); ?></p>
                </div>
                <div class="info-card">
                    <h4><?php echo t('contact_service'); ?></h4>
                    <p><?php echo t('contact_service_desc'); ?></p>
                </div>
                <div class="info-card">
                    <h4><?php echo t('contact_logistics'); ?></h4>
                    <p><?php echo t('contact_logistics_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>