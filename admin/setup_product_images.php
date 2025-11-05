<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Créer la table product_images
$sql = "CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_label VARCHAR(100) DEFAULT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

try {
    $db->exec($sql);
    echo "✅ Table product_images créée avec succès !<br>";
    
    // Migrer les images existantes
    $migrate_sql = "INSERT INTO product_images (product_id, image_path, is_primary, sort_order)
                    SELECT id, image_path, TRUE, 1 
                    FROM products 
                    WHERE image_path IS NOT NULL AND image_path != ''";
    
    $result = $db->exec($migrate_sql);
    echo "✅ $result images existantes migrées !<br>";
    
    echo "<br><a href='products.php'>← Retour aux produits</a>";
    
} catch(PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>