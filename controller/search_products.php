<?php
session_start();
include '../config/db.php';

// Initialize search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';

// Build the base query
$sql = 'SELECT p.id, p.name, c.category_name, p.price, p.qty, p.image, p.created_at,
        CASE WHEN w.wishlist_id IS NOT NULL THEN 1 ELSE 0 END as in_wishlist
        FROM products p 
        LEFT JOIN category c ON p.category_id = c.id 
        LEFT JOIN wishlist w ON w.product_id = p.id AND w.user_id = ? 
        WHERE 1=1';

$params = [$_SESSION['user_id'] ?? 0];

// Add search condition if search term is provided
if (!empty($search)) {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Add category filter
if (!empty($category)) {
    $sql .= ' AND p.category_id = ?';
    $params[] = $category;
}

// Add price range filter
if (!empty($price_range)) {
    $price_parts = explode('-', $price_range);
    if (count($price_parts) == 2) {
        $sql .= ' AND p.price BETWEEN ? AND ?';
        $params[] = $price_parts[0];
        $params[] = $price_parts[1];
    }
}

$sql .= ' ORDER BY p.created_at DESC';

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$products = [];
if ($stmt && $stmt->execute($params)) {
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Return products as HTML
foreach ($products as $product): ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card product-card h-100 shadow-sm">
            <a href="./details.php?id=<?php echo $product['id']; ?>"><img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>"></a>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title text-primary"><?php echo htmlspecialchars($product['name']); ?></h5>
                <div class="mb-2 text-muted small"><?php echo htmlspecialchars($product['category_name']); ?></div>
                <div class="mb-3 fw-bold text-success">Rs. <?php echo number_format($product['price'], 2); ?></div>                
                <div class="mt-auto d-flex gap-2">
                    <button type="button" class="btn btn-laflora btn-sm w-100 add-to-cart-btn" 
                            data-product-id="<?php echo $product['id']; ?>">
                        <i class="fa fa-cart-plus me-1"></i> Add to Cart
                    </button>                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 add-to-wishlist-btn<?php echo $product['in_wishlist'] ? ' active' : ''; ?>"
                            data-product-id="<?php echo $product['id']; ?>">
                        <i class="fa fa-heart me-1<?php echo $product['in_wishlist'] ? ' text-danger' : ''; ?>"></i> 
                        <?php echo $product['in_wishlist'] ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
