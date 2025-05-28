<?php
session_start();
    require_once '../../config/db.php';

    $success = '';
    $error = '';
    $edit_mode = false;
    $edit_product = null;

    // Fetch categories for the select dropdown
    $categories = [];
    $stmt = $conn->query('SELECT id, category_name FROM category ORDER BY category_name ASC');
    if ($stmt) {
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if editing an existing product
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $edit_mode = true;
        $product_id = intval($_GET['id']);
        $stmt = $conn->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$edit_product) {
            $error = 'Product not found.';
            $edit_mode = false;
        }
    }

    // Handle product add/update form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $image_path = $edit_product['image'] ?? '';

        // Handle image upload if a new image is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imgTmp = $_FILES['image']['tmp_name'];
            $imgName = basename($_FILES['image']['name']);
            $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($imgExt, $allowed)) {
                $newName = uniqid('prod_', true) . '.' . $imgExt;
                $uploadDir = '../../uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $dest = $uploadDir . $newName;
                if (move_uploaded_file($imgTmp, $dest)) {
                    $image_path = $newName;
                } else {
                    $error = 'Failed to upload image.';
                }
            } else {
                $error = 'Invalid image file type.';
            }
        } else if (!$edit_mode && empty($image_path)) {
            $error = 'Product image is required.';
        }

        // Insert or update product if no error
        if (empty($error)) {
            try {
                if ($edit_mode) {
                    $stmt = $conn->prepare('UPDATE products SET name = :name, category_id = :category_id, price = :price, qty = :stock, image = :image, description = :description WHERE id = :id');
                    $stmt->bindParam(':id', $product_id);
                } else {
                    $stmt = $conn->prepare('INSERT INTO products (name, category_id, price, qty, image, description) VALUES (:name, :category_id, :price, :stock, :image, :description)');
                }
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':image', $image_path);
                $stmt->bindParam(':description', $description);
                if ($stmt->execute()) {
                    $success = $edit_mode ? 'Product updated successfully!' : 'Product added successfully!';
                    if ($edit_mode) {
                        // Refresh product data after update
                        $stmt = $conn->prepare('SELECT * FROM products WHERE id = :id');
                        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                } else {
                    $error = $edit_mode ? 'Failed to update product.' : 'Failed to add product.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
?>


 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Product - Admin | Food Plaza</title>
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
                 <div class="d-flex justify-content-between align-items-center mb-4">
                     <h1 class="admin-title mb-0"><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Product</h1>
                     <a href="products.php" class="btn btn-theme"><i class="fas fa-arrow-left me-1"></i> Back to Products</a>
                 </div>
                 <div class="row justify-content-center">
                     <div class="col-lg-8">
                         <div class="card admin-card shadow-sm border-0 p-4">

                             <form method="post" enctype="multipart/form-data" action="">
                                 <?php if (!empty($success)): ?>
                                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                                         <?php echo $success; ?>
                                         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                     </div>
                                 <?php elseif (!empty($error)): ?>
                                     <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                         <?php echo $error; ?>
                                         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                     </div>
                                 <?php endif; ?>
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="productName" name="name" value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select bg-dark text-light border-accent shadow-sm rounded-3" id="category" name="category" required style="min-width: 180px; border-width: 2px; font-weight: 500; appearance: auto;">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php if (($edit_product['category_id'] ?? 0) == $cat['id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price (Rs.)</label>
                                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="<?php echo htmlspecialchars($edit_product['price'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($edit_product['qty'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <?php if (!empty($edit_product['image'])): ?>
                                        <img src="../../uploads/products/<?php echo htmlspecialchars($edit_product['image']); ?>" alt="Product Image" class="img-thumbnail mt-2" style="max-width: 120px;">
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-theme px-5"><i class="fas fa-save me-2"></i><?php echo $edit_mode ? 'Update' : 'Save'; ?> Product</button>
                                </div>
                             </form>
                         </div>
                     </div>
                 </div>
             </main>
         </div>
     </div>
     <!-- Bootstrap JS Bundle with Popper -->
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
 </body>

 </html>