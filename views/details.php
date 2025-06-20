<?php
session_start();
require_once '../config/db.php';

// Get product ID from query string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

if ($product_id > 0) {
    $user_id = $_SESSION['user_id'] ?? 0;

    $stmt = $conn->prepare('
        SELECT p.*, c.category_name AS category_name,
        CASE WHEN w.wishlist_id IS NOT NULL THEN 1 ELSE 0 END as in_wishlist
        FROM products p 
        LEFT JOIN category c ON p.category_id = c.id 
        LEFT JOIN wishlist w ON w.product_id = p.id AND w.user_id = :user_id
        WHERE p.id = :id
    ');
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - LaFlora</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts for Logo -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <!-- Google Fonts for body and headings -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Montserrat:wght@400;600&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../public/css/main.css">
  <style>
    .product-details-wrapper {
        padding: 2rem 0;
        background: var(--foodplaza-bg, #181a20);
        color: var(--foodplaza-dark, #fff);
    }
    .product-details-card {
        background: var(--foodplaza-surface, #23262f);
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.10);
        padding: 2.5rem !important;
        margin: 1rem 0;
        color: var(--foodplaza-dark, #fff);
        border: 1.5px solid var(--foodplaza-border, #282c37);
    }
    .product-image-container {
        background: var(--foodplaza-bg, #181a20);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .product-image {
        max-height: 400px;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
        background: var(--foodplaza-surface, #23262f);
        border-radius: 10px;
    }
    .product-image:hover {
        transform: scale(1.05);
    }
    .product-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 2.2rem;
        margin-bottom: 1rem;
        color: var(--foodplaza-primary, #ff5722);
    }
    .product-price {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--foodplaza-accent, #ffb300);
        margin-bottom: 1.5rem;
    }
    .category-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: var(--foodplaza-surface, #23262f);
        border-radius: 25px;
        color: var(--foodplaza-accent, #ffb300);
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    .product-description {
        font-size: 1.08rem;
        line-height: 1.8;
        color: var(--foodplaza-dark, #fff);
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--foodplaza-border, #282c37);
    }
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .btn-add-cart, .btn-laflora {
        background: var(--foodplaza-primary, #ff5722);
        border: none;
        color: #fff;
        padding: 0.8rem 2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 600;
        letter-spacing: 0.5px;
        flex: 2;
    }
    .btn-add-cart:hover, .btn-laflora:hover {
        background: var(--foodplaza-accent, #ffb300);
        color: var(--foodplaza-bg, #181a20);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 105, 180, 0.2);
    }
    .btn-wishlist, .btn-outline-secondary {
        background: var(--foodplaza-surface, #23262f);
        border: 2px solid var(--foodplaza-accent, #ffb300);
        color: var(--foodplaza-accent, #ffb300);
        padding: 0.8rem 2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 600;
        flex: 1;
    }
    .btn-wishlist:hover, .btn-outline-secondary:hover, .btn-outline-secondary.active {
        background: var(--foodplaza-primary, #ff5722);
        color: #fff;
        border-color: var(--foodplaza-primary, #ff5722);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.2);
    }
    .btn i {
        margin-right: 8px;
    }
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .product-details-card {
            padding: 1.5rem !important;
        }
        .product-title {
            font-size: 1.8rem;
        }
        .product-price {
            font-size: 1.5rem;
        }
        .action-buttons {
            flex-direction: column;
        }
        .btn-add-cart, .btn-wishlist, .btn-laflora, .btn-outline-secondary {
            width: 100%;
        }
    }
  </style>
</head>
<body>    <?php include_once '../includes/nav.php'; ?>
    <div class="product-details-wrapper">
        <div class="container">
            <?php if ($product): ?>
                <div class="row g-4 align-items-center product-details-card">
                    <div class="col-md-6">
                        <div class="product-image-container">
                            <img src="../uploads/products/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="img-fluid rounded product-image">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h2 class="mb-3 product-title" style="color:var(--primary-color)"><?= htmlspecialchars($product['name']) ?></h2>
                        <h4 class="mb-3 product-price" style="color:var(--accent-color)">Rs <?= number_format($product['price'], 2) ?></h4>
                        <p class="mb-2 text-muted"><strong>Category:</strong> <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></p>
                        <p class="mb-4 product-description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        
                        <div class="d-flex gap-2">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button type="button" class="btn btn-laflora add-to-cart-btn" 
                                        data-product-id="<?= $product['id'] ?>">
                                    <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                </button>                                <button type="button" class="btn btn-outline-secondary add-to-wishlist-btn<?php echo $product['in_wishlist'] ? ' active' : ''; ?>"
                                    data-product-id="<?= $product['id'] ?>">
                                    <i class="fa fa-heart me-1<?php echo $product['in_wishlist'] ? ' text-danger' : ''; ?>"></i> 
                                    <?php echo $product['in_wishlist'] ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                                </button>
                            <?php else: ?>
                                <a href="signin.php" class="btn btn-laflora">
                                    <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                </a>
                                <a href="signin.php" class="btn btn-outline-secondary">
                                    <i class="fa fa-heart me-1"></i> Add to Wishlist
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">Product not found.</div>
            <?php endif; ?>            </div>
        </div>
    </div>
    <?php include_once '../includes/footer.php'; ?>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JS -->
    <script src="../public/js/main.js"></script>
    <script src="../public/js/cart.js"></script>
    <script src="../public/js/wishlist.js"></script>
</body>
</html>