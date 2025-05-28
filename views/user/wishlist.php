<?php
session_start();
require '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$wishlist_items = [];

try {
    $stmt = $conn->prepare('
        SELECT w.wishlist_id, p.id AS product_id, p.name, p.price, p.qty AS stock, p.image, cat.category_name 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.id 
        LEFT JOIN category cat ON p.category_id = cat.id 
        WHERE w.user_id = ? 
        ORDER BY w.added_at DESC
    ');
    $stmt->execute([$user_id]);
    $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - LaFlora</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/main.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Sidebar -->
            <?php include_once('./includes/user_nav.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9 ms-auto content py-4">
                <h2 class="section-title themed-title mb-4">My Wishlist</h2>
                <div class="row g-4">
                    <?php if (empty($wishlist_items)) { ?>
                        <div class="col-12 text-center">
                            <div class="alert alert-info text-center" role="alert">
                                Your wishlist is empty. Start adding products you love!
                            </div>
                            <a href="../shop.php"><button class="btn btn-md text-white py-2 px-3 fw-bold" style="background-color: var(--laflora-secondary);">Add Now</button></a>
                        </div>
                    <?php } ?>
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0 bg-dark text-light" style="max-width: 320px; min-height: 320px; margin: 0 auto;">
                                <img src="../../uploads/products/<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>" style="height: 160px; object-fit: cover;">
                                <div class="card-body d-flex flex-column p-3">
                                    <h5 class="card-title text-truncate fw-bold text-white mb-2" style="font-size:1rem;"><?= htmlspecialchars($item['name']) ?></h5>
                                    <p class="card-text small text-accent mb-2" style="font-size:0.95rem;"> <?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?> </p>
                                    <p>Price: <span class="fw-bold text-accent" style="font-size:0.95rem;">Rs <?= number_format($item['price'], 2) ?></span></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/js/wishlist.js"></script>
</body>
</html>