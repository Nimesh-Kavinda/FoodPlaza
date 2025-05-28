<?php
session_start();
// Check if user is logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products - LaFlora</title>
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
                    <h1 class="admin-title mb-0">Products</h1>
                    <a href="./product_add.php" class="btn btn-theme"><i class="fas fa-plus me-1"></i> Add Product</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle bg-white">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once '../../config/db.php';

                            // Handle delete request
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
                                $delete_id = intval($_POST['delete_id']);
                                $stmt = $conn->prepare('DELETE FROM products WHERE id = :id');
                                $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
                                $stmt->execute();
                            }

                            // Fetch all products with category name
                            $products = [];
                            $sql = 'SELECT p.id, p.name, c.category_name, p.price, p.qty, p.image, p.created_at FROM products p LEFT JOIN category c ON p.category_id = c.id ORDER BY p.id DESC';
                            $stmt = $conn->query($sql);
                            if ($stmt) {
                                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }

                            if (!empty($products)):
                                foreach ($products as $prod): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prod['id']); ?></td>
                                        <td>
                                            <?php if (!empty($prod['image'])): ?>
                                                <img src="<?php echo '../../uploads/products/' . htmlspecialchars($prod['image']); ?>" alt="Product Image" style="width:60px; height:60px; object-fit:cover;">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($prod['name']); ?></td>
                                        <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                                        <td>Rs. <?php echo number_format($prod['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($prod['qty']); ?></td>
                                        <td><?php echo htmlspecialchars($prod['created_at']); ?></td>
                                        <td class="text-center">
                                            <a href="product_add.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-outline-primary me-2" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form method="post" action="" style="display:inline;" class="delete-product-form">
                                                <input type="hidden" name="delete_id" value="<?php echo $prod['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger delete-product-btn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr><td colspan="8" class="text-center">No products found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-product-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You won\'t be able to revert this!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e75480',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>