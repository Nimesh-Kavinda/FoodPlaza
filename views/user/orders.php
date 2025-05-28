<?php
    session_start();
    include '../../config/db.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../signin.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Process status updates if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['update_status'];
        
        // Only allow delivered or cancelled status updates
        if (in_array($new_status, ['complete', 'canceled'])) {
            $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE order_id = ? AND user_id = ? AND status NOT IN ("delivered", "cancelled")');
            if ($stmt->execute([$new_status, $order_id, $user_id])) {
                $success_message = "Order status updated successfully!";
            }
        }
    }

    // Fetch user's orders with item details and quantities
    $stmt = $conn->prepare('
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(p.name, " (", oi.quantity, ")") SEPARATOR ", ") as product_names
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ');
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - LaFlora</title>
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
            <?php include('./includes/user_nav.php'); ?>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9 ms-auto content py-4">
                <h2 class="section-title themed-title mb-4">My Orders</h2>
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-bordered table-hover rounded-4 overflow-hidden themed-table mb-0">
                            <thead class="table-dark border-bottom border-secondary">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Update Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="fw-semibold text-accent">#<?php echo $order['order_id']; ?></td>
                                            <td class="text-light"><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                                            <td>
                                                <?php
                                                $products = explode(', ', $order['product_names']);
                                                foreach ($products as $product) {
                                                    echo '<div class="mb-1 text-light">' . htmlspecialchars($product) . '</div>';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-light">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <?php
                                                    $status_class = match($order['status']) {
                                                        'pending' => 'bg-warning text-dark',
                                                        'processing' => 'bg-info text-dark',
                                                        'shipped' => 'bg-primary',
                                                        'complete', 'delivered' => 'bg-success',
                                                        'canceled', 'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    $status_text = ucfirst($order['status']);
                                                ?>
                                                <span class="badge px-3 py-2 fs-6 <?php echo $status_class; ?> shadow-sm" style="letter-spacing:0.5px;"><?php echo $status_text; ?></span>
                                            </td>
                                            <td>
                                                <?php if ($order['status'] !== 'complete' && $order['status'] !== 'canceled'): ?>
                                                <form method="POST" class="status-update-form d-flex align-items-center gap-2" data-order-id="<?php echo $order['order_id']; ?>">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <select name="update_status" class="form-select form-select-sm status-select bg-dark text-light border-accent shadow-sm rounded-3" style="min-width:150px; border-width:2px; font-weight:500; appearance: auto;" onchange="this.form.submit()">
                                                        <option value="">Update status...</option>
                                                        <option value="complete">Mark as Complete</option>
                                                        <option value="canceled">Cancel Order</option>
                                                    </select>
                                                </form>
                                                <?php else: ?>
                                                    <span class="text-white">No actions available</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="../order_confirmation.php?order_id=<?php echo $order['order_id']; ?>" 
                                                   class="btn btn-link p-0 view-order-btn text-accent fs-5" 
                                                   title="View Order Details"
                                                   style="color: var(--accent-color,rgb(221, 69, 9)) !important;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
