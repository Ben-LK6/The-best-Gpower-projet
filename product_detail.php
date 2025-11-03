<?php 
$page_title = "GPower - " . htmlspecialchars($product['name_fr']);
$css_file = "product_detail.css";
include 'includes/header.php'; 

include 'includes/tracking.php';
trackPageView('product', $product_id);

// Vérifier qu'un ID produit est passé
$product_id = $_GET['id'] ?? null;
if(!$product_id) {
    header('Location: products.php');
    exit;
}

// Connexion à la base
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer le produit
$query = "SELECT p.*, c.name_fr as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Tracking des vues
$view_query = "INSERT INTO product_views (product_id, ip_address, user_agent) VALUES (?, ?, ?)";
$view_stmt = $db->prepare($view_query);
$view_stmt->execute([$product_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);

// Si produit non trouvé
if(!$product) {
    header('Location: products.php');
    exit;
}
?>

<div class="ultra-simple-product">
    <div class="container">
        <a href="products.php" class="back-link">← Retour</a>
        
        <div class="product-block">
            <!-- Image -->
            <?php if($product['image_path']): ?>
                <img src="uploads/<?php echo $product['image_path']; ?>" alt="<?php echo $product['name_fr']; ?>" class="product-image">
            <?php else: ?>
                <div class="no-image">🛠️ Image non disponible</div>
            <?php endif; ?>

            <!-- Informations en bloc -->
            <div class="info-block">
                <h1><?php echo htmlspecialchars($product['name_fr']); ?></h1>
                <div class="category">Catégorie : <?php echo htmlspecialchars($product['category_name']); ?></div>
                <div class="price">Prix : <?php echo number_format($product['price'], 2, ',', ' '); ?> €</div>
                <div class="location">📍 Localisation : <?php echo htmlspecialchars($product['location']); ?></div>
                <div class="reference">Référence : GP-<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?></div>
                
                <div class="description">
                    <strong>Description :</strong><br>
                    <?php echo nl2br(htmlspecialchars($product['description_fr'] ?: 'Aucune description disponible.')); ?>
                </div>

                <!-- Contact direct -->
                <div class="contact-direct">
                    <div class="contact-info">
                        <div>📱 WhatsApp : +22940870199</div>
                        <div>📧 Email : generatorpower60@gmail.com</div>
                    </div>

                    <!-- Petits boutons -->
                    <div class="small-buttons">
                        <a href="https://wa.me/2250700000000?text=Bonjour%20GPower,%20je%20suis%20intéressé%20par%20le%20produit%20:%20<?php echo urlencode($product['name_fr']); ?>%20(Réf:%20GP-<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?>)" 
                           class="small-btn whatsapp-btn" target="_blank">
                            WhatsApp
                        </a>
                        <a href="mailto:contact@gpower.com?subject=Commande%20produit%20GPower%20-%20<?php echo urlencode($product['name_fr']); ?>" 
                           class="small-btn email-btn">
                            Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>