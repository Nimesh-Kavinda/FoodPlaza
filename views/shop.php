<?php
    session_start();
    include '../config/db.php';

    // Get categories for the filter
    $categories = [];
    $stmt = $conn->query('SELECT id, category_name FROM category ORDER BY id ASC');
    if ($stmt) {
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Food Plaza</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for Logo -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <!-- Google Fonts for body and headings -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Montserrat:wght@400;600&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">    <!-- Custom CSS -->
    <link rel="stylesheet" href="../public/css/main.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include_once('../includes/nav.php'); ?>

    <section class="shop-hero-section py-5">
        <div class="container">
            <h1 class="shop-title about-title mb-4 text-center">Order Food</h1>
            <div id="shopFilterForm">
                <div class="row mb-4 align-items-center g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control shop-search-input" id="shopSearch" 
                               placeholder="Search for food...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select shop-filter-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['id']); ?>">
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endforeach; ?>         
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select shop-filter-select" id="priceFilter">
                            <option value="">All Prices</option>
                            <option value="0-1000">Below Rs. 1000</option>
                            <option value="1000-1500">Rs. 1000 - Rs. 1500</option>
                            <option value="1500-2000">Rs. 1500 - Rs. 2000</option>
                            <option value="2000-99999">Above Rs. 2000</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-foodplaza w-100" id="clearFilters">Clear Filters</button>
                    </div>
                </div>
            </div>
            
            <div class="row g-4" id="shopProductGrid">
                <!-- Products will be loaded here by AJAX -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('../includes/footer.php'); ?>    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JS -->
    <script src="../public/js/main.js"></script>
    <script src="../public/js/search.js"></script>
    <script src="../public/js/cart.js"></script>
    <script src="../public/js/wishlist.js"></script>
</body>
</html>
