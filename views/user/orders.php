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
    <style>
        .status-select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.875rem;
            background-color: #fff;
            transition: all 0.3s ease;
            width: auto;
            min-width: 160px;
        }
        
        .status-select:hover, .status-select:focus {
            border-color: #FF69B4;
            box-shadow: 0 0 0 2px rgba(255, 105, 180, 0.1);
            outline: none;
        }

        .view-order-btn {
            color: #FF69B4 !important;
            transition: transform 0.2s ease, color 0.2s ease;
            font-size: 1.1rem;
        }

        .view-order-btn:hover {
            transform: scale(1.1);
            color: #ff1493 !important;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 6px;
        }

        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
        }

        .status-update-form {
            margin: 0;
        }

        .item-quantity {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="container">
            <div class="row content-wrapper">
                <!-- Sidebar -->
            <?php include('./includes/user_nav.php'); ?>

                <!-- Main Content -->
                <div class="col-md-9 col-lg-9 content">
                    <h2 class="section-title">My Orders</h2>
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                        <div class="table-responsive">
                            <table class="table align-middle table-bordered table-hover">
                                <thead class="table-light">
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
                                            <td colspan="6" class="text-center">No orders found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><?php echo $order['order_id']; ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                                                <td>
                                                    <?php
                                                    $products = explode(', ', $order['product_names']);
                                                    foreach ($products as $product) {
                                                        echo '<div class="mb-1">' . htmlspecialchars($product) . '</div>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
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
                                                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($order['status'] !== 'complete' && $order['status'] !== 'canceled'): ?>
                                                    <form method="POST" class="status-update-form" data-order-id="<?php echo $order['order_id']; ?>">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                        <select name="update_status" class="form-select form-select-sm status-select" onchange="this.form.submit()">
                                                            <option value="">Update status...</option>
                                                            <option value="complete">Mark as Complete</option>
                                                            <option value="canceled">Cancel Order</option>
                                                        </select>
                                                    </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">No actions available</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="../order_confirmation.php?order_id=<?php echo $order['order_id']; ?>" 
                                                       class="btn btn-link p-0 view-order-btn" 
                                                       title="View Order Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-update-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const orderId = form.dataset.orderId;
            const newStatus = formData.get('update_status');
            
            if (!newStatus) return; // Don't submit if no status selected
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.ok)
            .then(success => {
                if (success) {
                    // Update the status badge
                    const statusBadge = form.closest('tr').querySelector('.badge');
                    let newClass = '';
                    let statusText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    
                    switch(newStatus) {
                        case 'pending':
                            newClass = 'bg-warning text-dark';
                            break;
                        case 'processing':
                            newClass = 'bg-info text-dark';
                            break;
                        case 'shipped':
                            newClass = 'bg-primary';
                            break;
                        case 'complete':
                        case 'delivered':
                            newClass = 'bg-success';
                            break;
                        case 'canceled':
                        case 'cancelled':
                            newClass = 'bg-danger';
                            break;
                        default:
                            newClass = 'bg-secondary';
                    }
                    
                    statusBadge.className = 'badge ' + newClass;
                    statusBadge.textContent = statusText;
                    
                    // If order is complete or canceled, replace form with "No actions available"
                    if (newStatus === 'complete' || newStatus === 'canceled') {
                        form.replaceWith(document.createTextNode('No actions available'));
                    }
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                alert('Failed to update order status. Please try again.');
            });
        });
    });
});
</script>
