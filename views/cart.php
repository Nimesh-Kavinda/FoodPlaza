<?php
    session_start();
    include '../config/db.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: signin.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $cart_items = [];
    $cart_subtotal = 0;
    $cart_count = 0;

    // Fetch cart items with product details
    $stmt = $conn->prepare('
        SELECT c.cart_id, c.quantity, p.id as product_id, p.name, p.price, p.qty as stock, p.image, cat.category_name 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        LEFT JOIN category cat ON p.category_id = cat.id 
        WHERE c.user_id = ? 
        ORDER BY c.added_at DESC
    ');
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate cart totals
    foreach ($cart_items as $item) {
        $cart_subtotal += $item['price'] * $item['quantity'];
        $cart_count += $item['quantity'];
    }

    // SweetAlert2 for notifications
    if (isset($_SESSION['cart_message'])) {
        $message_type = $_SESSION['cart_message_type'] ?? 'success';
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{$message_type}',
                    title: '{$_SESSION['cart_message']}',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>";
        unset($_SESSION['cart_message'], $_SESSION['cart_message_type']);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Food Plaza</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for Logo -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <!-- Google Fonts for body and headings -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Montserrat:wght@400;600&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../public/css/main.css">
</head>
<body>
<!-- Navbar (same as other pages) -->
<?php include_once('../includes/nav.php'); ?>

<section class="cart-hero-section py-5">
    <div class="container">
        <h1 class="about-title mb-4 text-center">My Cart</h1>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div class="fs-5 text-secondary">
                <i class="fa fa-cart-shopping text-success me-2"></i>
                <?php echo $cart_count; ?> item<?php echo $cart_count !== 1 ? 's' : ''; ?> in your cart
            </div>
            <?php if ($cart_count > 0): ?>
            <button class="btn btn-outline-danger btn-sm px-4" id="clearCart">
                <i class="fa fa-trash me-1"></i> Clear All
            </button>
            <?php endif; ?>
        </div>
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="cart-list" id="cartList">
                    <?php if (empty($cart_items)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-cart-shopping fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Your cart is empty</h4>
                            <a href="shop.php" class="btn btn-foodplaza mt-3">
                                <i class="fas fa-shopping-bag me-2"></i>Order More Food
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="card cart-card mb-4 border-0 shadow-sm">
                                <div class="row g-0 align-items-center">
                                    <div class="col-4 col-md-3">
                                        <img src="../uploads/products/<?php echo htmlspecialchars($item['image'] ?: 'default.jpg'); ?>" 
                                             class="cart-img" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </div>
                                    <div class="col-8 col-md-9">
                                        <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3">
                                            <div class="flex-fill">
                                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                <div class="mb-1 text-muted small"><?php echo htmlspecialchars($item['category_name']); ?></div>
                                                <div class="fw-bold" style="color: var(--laflora-secondary); font-size: 1.1rem;">
                                                    Rs. <?php echo number_format($item['price'], 2); ?>
                                                </div>
                                            </div>
                                            <form class="d-flex align-items-center gap-2 mb-0">
                                                <button type="button" class="btn btn-outline-secondary btn-sm cart-qty-btn" 
                                                        name="decrease_qty" value="<?php echo $item['cart_id']; ?>">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control cart-qty-input" 
                                                       name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="<?php echo $item['stock']; ?>" style="width: 55px;">
                                                <button type="button" class="btn btn-outline-secondary btn-sm cart-qty-btn" 
                                                        name="increase_qty" value="<?php echo $item['cart_id']; ?>">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </form>
                                            <button class="btn btn-outline-danger btn-sm cart-remove" 
                                                    value="<?php echo $item['cart_id']; ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($cart_items)): ?>
                <div class="col-lg-4">
                    <div class="cart-summary card shadow-sm border-0 p-4 sticky-top" style="top: 100px;">
                        <h4 class="mb-3" style="color: var(--laflora-primary);">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items (<?php echo $cart_count; ?>)</span>
                            <span>
                                <?php
                                $items_text = array_map(function($item) {
                                    return "Rs. " . number_format($item['price'], 2) . " × " . $item['quantity'];
                                }, $cart_items);
                                echo implode(' + ', $items_text);
                                ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span class="fw-bold" id="cartSubtotal">Rs. <?php echo number_format($cart_subtotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-5 fw-bold text-success" id="cartTotal">
                                Rs. <?php echo number_format($cart_subtotal, 2); ?>
                            </span>
                        </div>
                        <a href="checkout.php" class="btn btn-foodplaza w-100 py-2 fs-5">
                            <i class="fa fa-credit-card me-2"></i>Checkout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 p-4 text-center">
                        <i class="fas fa-sad-tear fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Your cart is empty</h5>
                        <p class="text-secondary">Add items to your cart to see them here.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer (same as other pages) -->
<?php include_once('../includes/footer.php'); ?>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
<!-- Custom JS -->
<script src="../public/js/cart.js"></script>
</body>
</html>