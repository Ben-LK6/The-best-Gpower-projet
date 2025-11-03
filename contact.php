<?php 
$page_title = "GPower - Contact";
$css_file = "contact.css";
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
        <h1>Nous Contacter</h1>
    </div>
</div>

<section class="contact-simple">
    <div class="container">
        <div class="contact-description">
            <p>Contactez-nous directement pour toute commande ou information sur nos produits.</p>
        </div>

        <!-- WhatsApp -->
        <div class="contact-line">
            <div class="contact-info">
                <div class="contact-icon">📱</div>
                <div class="contact-details">
                    <h3>WhatsApp</h3>
                    <p>Réponse immédiate</p>
                </div>
            </div>
            <?php if($product_name): ?>
                <a href="https://wa.me/22940870199?text=Bonjour%20GPower,%20je%20suis%20intéressé%20par%20le%20produit%20:%20<?php echo urlencode($product_name); ?>%20(Réf:%20GP-<?php echo str_pad($product_id, 4, '0', STR_PAD_LEFT); ?>)" 
                   class="simple-btn whatsapp" target="_blank">
                    Contacter
                </a>
            <?php else: ?>
                <a href="https://wa.me/22940870199?text=Bonjour%20GPower,%20je%20souhaite%20obtenir%20des%20informations%20sur%20vos%20produits" 
                   class="simple-btn whatsapp" target="_blank">
                    Contacter
                </a>
            <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="contact-line">
            <div class="contact-info">
                <div class="contact-icon">📧</div>
                <div class="contact-details">
                    <h3>Email</h3>
                    <p>Réponse sous 24h</p>
                </div>
            </div>
            <?php if($product_name): ?>
                <a href="mailto:generatorpower60@gmail.com?subject=Demande%20d'information%20:%20<?php echo urlencode($product_name); ?>" 
                   class="simple-btn email">
                    Contacter
                </a>
            <?php else: ?>
                <a href="mailto:generatorpower60@gmail.com?subject=Demande%20d'information%20GPower" 
                   class="simple-btn email">
                    Contacter
                </a>
            <?php endif; ?>
        </div>

        <!-- Téléphone -->
        <div class="contact-line">
            <div class="contact-info">
                <div class="contact-icon">📞</div>
                <div class="contact-details">
                    <h3>Téléphone</h3>
                    <p>+22940870199</p>
                </div>
            </div>
            <a href="tel:+22940870199" class="simple-btn phone">
                Appeler
            </a>
        </div>

        <!-- Informations pratiques -->
        <div class="practical-info">
            <h3>Informations pratiques</h3>
            <div class="info-items">
                <div class="info-item">
                    <strong>Horaires :</strong> Lun - Ven: 8h-18h
                </div>
                <div class="info-item">
                    <strong>Service :</strong> Devis et conseils personnalisés
                </div>
                <div class="info-item">
                    <strong>Livraison :</strong> Partout en Côte d'Ivoire
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>