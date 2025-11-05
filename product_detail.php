<?php 
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

// Récupérer toutes les images du produit (avec vérification si la table existe)
$product_images = [];
try {
    $images_query = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC";
    $images_stmt = $db->prepare($images_query);
    $images_stmt->execute([$product_id]);
    $product_images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Table n'existe pas encore, utiliser l'image principale du produit
    $product_images = [];
}

// Si produit non trouvé
if(!$product) {
    header('Location: products.php');
    exit;
}

// Inclure le système de langues et header
require_once 'includes/language.php';
$page_title = "GPower - " . htmlspecialchars($product['name_fr']);
$css_file = "product_detail.css";
include 'includes/header.php';

include 'includes/tracking.php';
trackPageView('product', $product_id);

// Tracking des vues
$view_query = "INSERT INTO product_views (product_id, ip_address, user_agent) VALUES (?, ?, ?)";
$view_stmt = $db->prepare($view_query);
$view_stmt->execute([$product_id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
?>

<div class="ultra-simple-product">
    <div class="container">
        <a href="products.php" class="back-link"><?php echo t('back'); ?></a>
        
        <div class="product-block">
            <!-- Images Gallery -->
            <?php if(!empty($product_images)): ?>
                <div class="product-gallery">
                    <div class="main-image">
                        <img src="uploads/<?php echo $product_images[0]['image_path']; ?>" 
                             alt="<?php echo $product['name_fr']; ?>" 
                             class="product-image" id="mainImage">
                        <?php if($product_images[0]['image_label']): ?>
                            <div class="image-label"><?php echo htmlspecialchars($product_images[0]['image_label']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(count($product_images) > 1): ?>
                        <div class="image-thumbnails">
                            <?php foreach($product_images as $index => $image): ?>
                                <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     onclick="changeMainImage('uploads/<?php echo $image['image_path']; ?>', '<?php echo htmlspecialchars($image['image_label'] ?? ''); ?>', this)">
                                    <img src="uploads/<?php echo $image['image_path']; ?>" alt="Thumbnail">
                                    <?php if($image['image_label']): ?>
                                        <span class="thumb-label"><?php echo htmlspecialchars($image['image_label']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif($product['image_path']): ?>
                <img src="uploads/<?php echo $product['image_path']; ?>" alt="<?php echo $product['name_fr']; ?>" class="product-image">
            <?php else: ?>
                <div class="no-image">🛠️ Image non disponible</div>
            <?php endif; ?>

            <!-- Informations en bloc -->
            <div class="info-block">
                <h1><?php echo htmlspecialchars($product['name_fr']); ?></h1>
                <div class="category"><?php echo t('products_category'); ?> : <?php echo htmlspecialchars($product['category_name']); ?></div>
                <div class="price"><?php echo t('products_price'); ?>: $<?php echo number_format($product['price'], 2, '.', ','); ?></div>
                <div class="location">📍 : <?php echo htmlspecialchars($product['location']); ?></div>
                <div class="reference"><?php echo t('reference'); ?> : GP-<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?></div>
                
                <div class="description">
                    <strong><?php echo t('description'); ?> :</strong><br>
                    <?php echo nl2br(htmlspecialchars($product['description_fr'] ?: t('no_description'))); ?>
                </div>

                <!-- Contact direct -->
                <div class="contact-direct">
                    <div class="contact-info">
                        <div class="contact-icons">
                            <?php 
                            require_once 'includes/whatsapp_helper.php';
                            echo getWhatsAppButton('22940870199', t('whatsapp_product') . ' : ' . $product['name_fr'], 'large');
                            echo getGmailButton('generatorpower60@gmail.com', t('whatsapp_product') . ' GPower: ' . $product['name_fr'], 'large');
                            ?>
                        </div>
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

<style>
.product-gallery {
    width: 100%;
}

.main-image {
    position: relative;
    margin-bottom: 15px;
}

.main-image .product-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 12px;
}

.image-label {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
}

.image-thumbnails {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 10px 0;
}

.thumbnail {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s ease;
    position: relative;
}

.thumbnail.active {
    border-color: #3b82f6;
    transform: scale(1.05);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumb-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.8);
    color: white;
    font-size: 10px;
    padding: 2px 4px;
    text-align: center;
}

@media (max-width: 768px) {
    .image-thumbnails {
        justify-content: center;
    }
    
    .thumbnail {
        width: 60px;
        height: 60px;
    }
}
</style>

<script>
function changeMainImage(imageSrc, imageLabel, thumbnailElement) {
    const mainImage = document.getElementById('mainImage');
    const labelElement = document.querySelector('.image-label');
    
    // Changer l'image principale
    mainImage.src = imageSrc;
    
    // Mettre à jour le label
    if (labelElement) {
        if (imageLabel) {
            labelElement.textContent = imageLabel;
            labelElement.style.display = 'block';
        } else {
            labelElement.style.display = 'none';
        }
    } else if (imageLabel) {
        const newLabel = document.createElement('div');
        newLabel.className = 'image-label';
        newLabel.textContent = imageLabel;
        document.querySelector('.main-image').appendChild(newLabel);
    }
    
    // Mettre à jour les thumbnails actifs
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    thumbnailElement.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>