<?php
// Include database connection
session_start();
require_once '../../config/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        try {
            $stmt = $conn->prepare('INSERT INTO category (category_name) VALUES (:name)');
            $stmt->bindParam(':name', $category_name, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $success = 'Category added successfully!';
            } else {
                $error = 'Error adding category.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = 'Category name cannot be empty.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category - Admin | Food Plaza</title>
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
                    <h1 class="admin-title mb-0">Add Category</h1>
                    <a href="categories.php" class="btn btn-theme"><i class="fas fa-arrow-left me-1"></i> Back to Categories</a>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card admin-card product-add-card shadow-sm border-0 p-4" style="background-color: var(--foodplaza-surface);">

                            <form method="post" action="">
                                <?php if (!empty($success)): ?>
                                    <div class="alert alert-success"><?php echo $success; ?></div>
                                <?php elseif (!empty($error)): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="categoryName" name="category_name" placeholder="Type category name" required>
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-theme px-5"><i class="fas fa-save me-2"></i>Save Category</button>
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