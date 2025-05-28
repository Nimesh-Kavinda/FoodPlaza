<?php 
session_start();
include '../../config/db.php';
  $users = [];
  $stmt = $conn->query('SELECT id, name, email, role FROM users ORDER BY id ASC');
  if ($stmt) {
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } 
  $products = [];
  $sql = 'SELECT p.id, p.name, c.category_name, p.price, p.qty, p.image, p.created_at FROM products p LEFT JOIN category c ON p.category_id = c.id ORDER BY p.id DESC';
  $stmt = $conn->query($sql);
  if ($stmt) {
      $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  $categories = [];
  $stmt = $conn->query('SELECT id, category_name FROM category ORDER BY id ASC');
  if ($stmt) {
      $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  $orders = [];
  $stmt = $conn->query('SELECT order_id, user_id, total_amount, status, order_date FROM orders ORDER BY order_id DESC');
  if ($stmt) {
      $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Plaza</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/main.css">
</head>
<body>
    <div class="container-fluid min-vh-100 admin-bg">
        <div class="row h-100">
            <!-- Sidebar -->
            <?php include_once('./includes/admin_nav.php'); ?>
            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-5 py-4">
                <h1 class="mb-4 admin-title">Welcome, Admin!</h1>
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card admin-card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="fas fa-box-open fa-2x mb-2 text-primary"></i>
                                <h5 class="card-title mb-1">Products</h5>
                                <p class="card-text text-muted"><?php echo count($products); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card admin-card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="fas fa-tags fa-2x mb-2 text-success"></i>
                                <h5 class="card-title mb-1">Categories</h5>
                                <p class="card-text text-muted"><?php echo count($categories); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card admin-card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2 text-warning"></i>
                                <h5 class="card-title mb-1">Orders</h5>
                                <p class="card-text text-muted"><?php echo count($orders); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card admin-card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-2 text-danger"></i>
                                <h5 class="card-title mb-1">Users</h5>
                                <p class="card-text text-muted"><?php echo count($users); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add more dashboard widgets or charts as needed -->
                <div class="mt-5">
                    <h2 class="h4 mb-3">Quick Links</h2>
                    <a href="products.php" class="btn btn-theme me-2 mb-2"><i class="fas fa-box-open me-1"></i> Manage Products</a>
                    <a href="categories.php" class="btn btn-theme me-2 mb-2"><i class="fas fa-tags me-1"></i> Manage Categories</a>
                    <a href="orders.php" class="btn btn-theme me-2 mb-2"><i class="fas fa-shopping-cart me-1"></i> Manage Orders</a>
                    <a href="users.php" class="btn btn-theme me-2 mb-2"><i class="fas fa-users me-1"></i> Manage Users</a>
                    <a href="feedbacks.php" class="btn btn-theme mb-2"><i class="fas fa-comments me-1"></i> Manage Feedback</a>
                </div>
            </main>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
