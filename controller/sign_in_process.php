<?php

include_once '../config/db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    session_start();
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['role'];

    header("Location: ../index.php?login=success");
    exit();
} else {
    echo "<script>window.location.href = '../views/signin.php?login=error';</script>";
}
?>
