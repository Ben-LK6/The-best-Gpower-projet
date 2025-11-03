<?php 
$page_title = "GPower - Nos Produits";
$css_file = "product.css";
include 'includes/header.php'; 

include 'includes/tracking.php';
trackPageView('products');

// Connexion à la base
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer toutes les catégories pour les filtres
$categories_query = "SELECT * FROM categories ORDER BY name_fr";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les filtres
$category_filter = $_GET['category'] ?? 'all';
$search_term = $_GET['search'] ?? '';

// Construire la requête produits avec filtres
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

// Construction de la requête finale
$products_query = "SELECT p.*, c.name_fr as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id";

if(!empty($where_conditions)) {
    $products_query .= " WHERE " . implode(" AND ", $where_conditions);
}

$products_query .= " ORDER BY p.created_at DESC";

$products_stmt = $db->prepare($products_query);
$products_stmt->execute($params);

// Compter le nombre total de produits
$total_products = $products_stmt->rowCount();

// Trouver le nom de la catégorie active
$active_category_name = 'Toutes les catégories';
if($category_filter !== 'all') {
    foreach($categories as $cat) {
        if($cat['id'] == $category_filter) {
            $active_category_name = $cat['name_fr'];
            break;
        }
    }
}
?>

<div class="page-simple-header">
    <div class="container">
        <h1>Nos Produits</h1>
    </div>
</div>

<section class="products-section">
    <div class="container">
        <!-- STRUCTURE ULTRA PRO -->
        <div class="search-filters-container">
            <!-- Barre de recherche minimaliste -->
            <div class="search-bar-minimal">
                <form method="GET" action="products.php" class="search-form-minimal" id="searchForm">
                    <div class="search-input-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="search" placeholder="Rechercher un produit..." 
                               value="<?php echo htmlspecialchars($search_term); ?>" 
                               id="searchInput" autocomplete="off">
                        <!-- Le champ catégorie sera géré par JavaScript -->
                    </div>
                </form>
            </div>

            <!-- Sélecteur de catégories déroulant -->
            <div class="category-dropdown">
                <button class="dropdown-toggle" type="button">
                    <span class="dropdown-icon">📁</span>
                    <span class="dropdown-text"><?php echo $active_category_name; ?></span>
                    <span class="dropdown-arrow">▼</span>
                </button>
                <div class="dropdown-menu">
                    <a href="javascript:void(0)" 
                       class="dropdown-item category-option <?php echo $category_filter === 'all' ? 'active' : ''; ?>" 
                       data-category="all">
                        Toutes les catégories
                    </a>
                    <?php foreach($categories as $category): ?>
                    <a href="javascript:void(0)" 
                       class="dropdown-item category-option <?php echo $category_filter == $category['id'] ? 'active' : ''; ?>" 
                       data-category="<?php echo $category['id']; ?>">
                        <?php echo $category['name_fr']; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Liste des produits -->
        <div class="products-grid">
            <?php while ($product = $products_stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="product-card">
                <!-- Lien sur toute la carte (sauf le bouton) -->
                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-card-link">
                    <div class="product-image">
                        <?php if($product['image_path']): ?>
                            <img src="uploads/<?php echo $product['image_path']; ?>" alt="<?php echo $product['name_fr']; ?>">
                        <?php else: ?>
                            <div class="no-image">🛠️ Image non disponible</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name_fr']; ?></h3>
                        <p class="product-category"><?php echo $product['category_name']; ?></p>
                        <p class="product-price"><?php echo number_format($product['price'], 2, ',', ' '); ?> €</p>
                        <p class="product-location">📍 <?php echo $product['location']; ?></p>
                    </div>
                </a>
                
                <!-- Bouton contact séparé -->
                <div class="product-contact-btn">
                    <a href="contact.php?product=<?php echo $product['id']; ?>" class="btn-contact" title="Contacter pour acheter">
                        📞
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
            
            <?php if($total_products == 0): ?>
                <div class="no-products">
                    <p>🛒 Aucun produit trouvé.</p>
                    <?php if(!empty($search_term)): ?>
                        <p>Essayez avec d'autres termes de recherche.</p>
                    <?php endif; ?>
                    <a href="products.php" class="btn btn-primary">Voir tous les produits</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const categoryOptions = document.querySelectorAll('.category-option');
    
    let currentCategory = '<?php echo $category_filter; ?>';
    let searchTimeout;

    // Dropdown des catégories
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
            this.classList.toggle('open');
        });
        
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
            dropdownToggle.classList.remove('open');
        });
        
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // RECHERCHE AVEC DÉLAI (sans AJAX)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            updateCategoryField();
            searchForm.submit();
        }, 500);
    });

    // Sélection de catégorie
    categoryOptions.forEach(option => {
        option.addEventListener('click', function() {
            currentCategory = this.getAttribute('data-category');
            
            // Mettre à jour l'apparence
            categoryOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Mettre à jour le texte du dropdown
            document.querySelector('.dropdown-text').textContent = this.textContent;
            
            // Fermer le dropdown
            dropdownMenu.classList.remove('show');
            dropdownToggle.classList.remove('open');
            
            // Mettre à jour le champ et soumettre
            updateCategoryField();
            searchForm.submit();
        });
    });

    // Fonction pour mettre à jour le champ caché de catégorie
    function updateCategoryField() {
        let categoryInput = document.getElementById('categoryInput');
        if (currentCategory !== 'all') {
            if (!categoryInput) {
                categoryInput = document.createElement('input');
                categoryInput.type = 'hidden';
                categoryInput.name = 'category';
                categoryInput.id = 'categoryInput';
                searchForm.appendChild(categoryInput);
            }
            categoryInput.value = currentCategory;
        } else {
            if (categoryInput) {
                categoryInput.remove();
            }
        }
    }

    // Focus automatique
    setTimeout(() => {
        if (searchInput) {
            searchInput.focus();
            const length = searchInput.value.length;
            searchInput.setSelectionRange(length, length);
        }
    }, 100);
});
</script>
<?php include 'includes/footer.php'; ?>