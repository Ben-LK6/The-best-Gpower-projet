<?php
require_once 'config/database.php';
require_once 'includes/language.php';

$database = new Database();
$db = $database->getConnection();

$category_filter = $_GET['category'] ?? 'all';
$search_term = $_GET['search'] ?? '';

// Construire la requête
$where_conditions = [];
$params = [];

if($category_filter !== 'all' && !empty($category_filter)) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
}

if(!empty($search_term)) {
    $where_conditions[] = "(p.name_fr LIKE ? OR p.description_fr LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}

$products_query = "SELECT p.*, c.name_fr as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id";

if(!empty($where_conditions)) {
    $products_query .= " WHERE " . implode(" AND ", $where_conditions);
}

$products_query .= " ORDER BY p.created_at DESC";

$products_stmt = $db->prepare($products_query);
$products_stmt->execute($params);

$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

if(empty($products)) {
    echo '<div class="no-products">
            <p>' . t('products_no_products') . '</p>';
    if(!empty($search_term)) {
        echo '<p>' . t('products_try_other') . '</p>';
    }
    echo '<a href="products.php" class="btn btn-primary">' . t('products_see_all') . '</a>
          </div>';
} else {
    foreach($products as $product) {
        echo '<div class="product-card">
                <a href="product_detail.php?id=' . $product['id'] . '" class="product-card-link">
                    <div class="product-image">';
        
        if($product['image_path']) {
            echo '<img src="uploads/' . $product['image_path'] . '" alt="' . htmlspecialchars($product['name_fr']) . '">';
        } else {
            echo '<div class="no-image">🛠️ Image non disponible</div>';
        }
        
        echo '    </div>
                    <div class="product-info">
                        <h3>' . htmlspecialchars($product['name_fr']) . '</h3>
                        <p class="product-category">' . htmlspecialchars($product['category_name']) . '</p>
                        <p class="product-price">$' . number_format($product['price'], 2, '.', ',') . '</p>
                        <p class="product-location">📍 ' . htmlspecialchars($product['location']) . '</p>
                    </div>
                </a>
                <div class="product-contact-btn">
                    <a href="https://wa.me/22940870199?text=' . urlencode(t('whatsapp_product') . ' : ' . $product['name_fr'] . ' (Ref: GP-' . str_pad($product['id'], 4, '0', STR_PAD_LEFT) . ')') . '" class="whatsapp-icon-btn whatsapp-large" target="_blank" title="Contacter via WhatsApp">
                        <img src="images/Whatsap.avif" alt="WhatsApp" class="whatsapp-img">
                    </a>
                </div>
              </div>';
    }
}
?>