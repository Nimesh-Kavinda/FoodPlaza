<?php
session_start();
require '../../config/db.php';

if (isset($_POST['update_password'])) {
    $currentPassword = $_POST['current-password'];
    $newPassword     = $_POST['new-password'];
    $confirmPassword = $_POST['confirm-password'];

    $userId = $_SESSION['user_id'];

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'All fields are required.'];
        header('Location: profile.php'); // redirect back
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['alert'] = ['type' => 'warning', 'message' => 'New passwords do not match.'];
        header('Location: profile.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Current password is incorrect.'];
        header('Location: profile.php');
        exit;
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updated = $updateStmt->execute([$hashedPassword, $userId]);

    if ($updated) {
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Password updated successfully.'];
    } else {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Error updating password.'];
    }

    header('Location: profile.php');
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - LaFlora</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/main.css">
</head>
<body>
    <div class="page-container">
        <div class="container">
            <div class="row content-wrapper">
                <!-- Sidebar -->
                <?php include_once('./includes/user_nav.php'); ?>

                <!-- Main Content -->
                <div class="col-md-9 col-lg-9 content">
                    <h2 class="section-title">My Profile</h2>
                    
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" value="<?php echo $_SESSION['name']; ?>" readonly autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="<?php echo $_SESSION['email']; ?>" readonly autocomplete="off">
                        </div>
                                            

                    <div class="password-section">
                        <h3 class="section-title">Change Password</h3>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="current-password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current-password" name="current-password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new-password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new-password" name="new-password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="update_password">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <?php if (isset($_SESSION['alert'])): ?>
<script>
    Swal.fire({
        icon: '<?= $_SESSION['alert']['type'] ?>',
        title: '<?= $_SESSION['alert']['type'] === 'success' ? "Success" : "Oops!" ?>',
        text: '<?= $_SESSION['alert']['message'] ?>',
        confirmButtonColor: '#3085d6'
    });
</script>
<?php unset($_SESSION['alert']); endif; ?>
</body>
</html>