<?php
require_once '../../config/db.php';

$success = '';
$error = '';

// Handle feedback status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'], $_POST['feedback_status'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $feedback_status = in_array($_POST['feedback_status'], ['inactive', 'active']) ? $_POST['feedback_status'] : 'inactive';
    $stmt = $conn->prepare('UPDATE feedback SET status = :status WHERE f_id = :id');
    $stmt->bindParam(':status', $feedback_status);
    $stmt->bindParam(':id', $feedback_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $success = 'Feedback status updated successfully!';
    } else {
        $error = 'Failed to update feedback status.';
    }
}

// Handle feedback delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback_id'])) {
    $feedback_id = intval($_POST['delete_feedback_id']);
    $stmt = $conn->prepare('DELETE FROM feedback WHERE f_id = :id');
    $stmt->bindParam(':id', $feedback_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $success = 'Feedback deleted successfully!';
    } else {
        $error = 'Failed to delete feedback.';
    }
}

// Fetch all feedback with user details
$feedbacks = [];
$stmt = $conn->query('
    SELECT f.f_id, f.message, f.status, u.name as user_name, u.email as user_email 
    FROM feedback f 
    LEFT JOIN users u ON f.user_id = u.id 
    ORDER BY f.f_id DESC
');
if ($stmt) {
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management - Food Plaza</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Montserrat:wght@700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container-fluid min-vh-100 admin-bg">
        <div class="row h-100">
            <!-- Sidebar -->
            <?php include_once('./includes/admin_nav.php'); ?>
            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-5 py-4">
                <h1 class="admin-title mb-4">Feedback Management</h1>
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle category-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedbacks as $feedback): ?>
                                <tr>
                                    <td><?= htmlspecialchars($feedback['f_id']) ?></td>
                                    <td><?= htmlspecialchars($feedback['user_name'] ?? 'Unknown User') ?></td>
                                    <td><?= htmlspecialchars($feedback['user_email'] ?? 'No Email') ?></td>
                                    <td>
                                        <div class="feedback-text" style="max-width: 300px; word-wrap: break-word;">
                                            <?= htmlspecialchars(mb_strimwidth($feedback['message'], 0, 100, '...')) ?>
                                            <?php if (mb_strlen($feedback['message']) > 100): ?>
                                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#feedbackModal<?= $feedback['f_id'] ?>">
                                                    Read More
                                                </button>
                                                <!-- Modal for full feedback -->
                                                <div class="modal fade" id="feedbackModal<?= $feedback['f_id'] ?>" tabindex="-1" aria-labelledby="feedbackModalLabel<?= $feedback['f_id'] ?>" aria-hidden="true">
                                                  <div class="modal-dialog">
                                                    <div class="modal-content">
                                                      <div class="modal-header">
                                                        <h5 class="modal-title" id="feedbackModalLabel<?= $feedback['f_id'] ?>">Full Feedback</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                      </div>
                                                      <div class="modal-body">
                                                        <?= nl2br(htmlspecialchars($feedback['message'])) ?>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="feedback_id" value="<?= htmlspecialchars($feedback['f_id']) ?>">
                                            <select name="feedback_status" class="form-select form-select-sm user-type-select" onchange="this.form.submit()">
                                                <option value="inactive" <?= $feedback['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                <option value="active" <?= $feedback['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" class="d-inline delete-feedback-form">
                                            <input type="hidden" name="delete_feedback_id" value="<?= htmlspecialchars($feedback['f_id']) ?>">
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-feedback-btn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($feedbacks)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Feedback Found</h4>
                        <p class="text-muted">There are no feedback entries in the system yet.</p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>