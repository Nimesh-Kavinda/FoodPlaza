<?php
    session_start();
    include '../config/db.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: signin.php');
        exit;
    }

    // Check if order_id is provided
    if (!isset($_GET['order_id'])) {
        header('Location: cart.php');
        exit;
    }

    $order_id = $_GET['order_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch order details
    $stmt = $conn->prepare('
        SELECT o.*, u.name as customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.order_id = ? AND o.user_id = ?
    ');
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // Redirect if order not found or doesn't belong to user
    if (!$order) {
        header('Location: cart.php');
        exit;
    }

    // Fetch order items
    $stmt = $conn->prepare('
        SELECT oi.*, p.name as product_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ');
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation - LaFlora</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts for Logo -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <!-- Google Fonts for body and headings -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Montserrat:wght@400;600&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../public/css/main.css">
</head>
<body>
<!-- Navbar (same as other pages) -->
 <?php include_once('../includes/nav.php'); ?>

<section class="order-confirm-section py-5">
  <div class="container">
    <div class="d-flex flex-column align-items-center mb-4">
      <div class="order-complete-icon mb-3">
        <i class="fa fa-circle-check fa-4x text-success"></i>
      </div>
      <h1 class="about-title text-center mb-2">Order Confirmed!</h1>
      <div class="fs-5 text-secondary text-center mb-4">Thank you for choosing us! Your order has been placed successfully.</div>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-7 col-xl-6">
        <div class="card receipt-card shadow-sm border-0 p-4 mb-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-bold">Order #</span>
            <span><?php echo htmlspecialchars($order_id); ?></span>
          </div>
          <div class="mb-2">
            <?php foreach ($order_items as $item): ?>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                <span>Rs. <?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-2">
            <span>Subtotal</span>
            <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Delivery</span>
            <span>Free</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Payment Method</span>
            <span><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></span>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-3">
            <span class="fs-5 fw-bold">Total</span>
            <span class="fs-5 fw-bold text-success">Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
          </div>          <div class="mt-3">
            <h6 class="mb-2">Shipping Details:</h6>
            <p class="mb-2">
              <strong>Customer Name:</strong> 
              <?php echo htmlspecialchars($order['customer_name']); ?>
            </p>
            <p class="mb-1">
              <strong>Phone:</strong> 
              <?php echo htmlspecialchars($order['phone']); ?>
            </p>
            <?php 
                $address_parts = explode("'", $order['shipping_address']);
                $labels = ['Street', 'City', 'Province', 'Postal Code', 'Country'];
                foreach ($address_parts as $index => $part):
            ?>
                <p class="mb-1">
                    <strong><?php echo $labels[$index]; ?>:</strong> 
                    <?php echo htmlspecialchars(trim($part)); ?>
                </p>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="text-center">
          <a href="../index.php" class="btn btn-laflora me-2"><i class="fa fa-home me-2"></i>Home</a>
          <a href="user/orders.php" class="btn btn-outline-laflora"><i class="fa fa-box me-2"></i>My Orders</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer (same as other pages) -->
 <?php include_once('../includes/footer.php'); ?>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="../public/js/main.js"></script>
</body>
</html>