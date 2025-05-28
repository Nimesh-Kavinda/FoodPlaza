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

    <style>
        /* Wishlist Card Styling */
.card {
    height: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    background-color: #fff;
    position: relative;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    border-color: #d0d0d0;
}

.card-img-top {
    height: 220px;
    object-fit: cover;
    border-bottom: 1px solid #f0f0f0;
}

.card-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-text {
    font-size: 0.9rem;
    color: #777;
}

.card .btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    transition: all 0.2s;
}

.card .btn:hover {
    opacity: 0.9;
}

    </style>

<body>
    <div class="page-container">
        <div class="container">
            <div class="row content-wrapper">
                <!-- Sidebar -->
                <?php include_once('./includes/user_nav.php'); ?>

                <!-- Main Content -->
                <div class="col-md-9 col-lg-9 content">
                    <h2 class="section-title">My Wishlist</h2>
                    <div class="row g-4">
                        <!-- Example wishlist item card -->
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
                                <div class="card h-100 shadow-sm border-0">
                                    <img src="../../uploads/products/<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-truncate"><?= htmlspecialchars($item['name']) ?></h5>
                                        <p class="card-text small text-muted mb-2">
                                            <?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/js/wishlist.js"></script>

</body>

</html>