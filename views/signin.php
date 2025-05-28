<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - Food Plaza</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts for Logo -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <!-- Google Fonts for body and headings -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Montserrat:wght@400;600&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../public/css/main.css">
</head>
<body class="signin-page">
  <div class="signin-card">
   <a href="../index.html" style="text-decoration: none;"> <div class="signin-logo" style="color: #fff !important;">Food Plaza <i class="fa-solid fa-utensils"></i></div> </a>
    <div class="signin-title">Sign In to Your Account</div>
    <?php
        if (isset($_GET['success'])) {
    echo "<p class = 'text-center' style='color: green;'>".htmlspecialchars($_GET['success'])."</p>";
        }
    ?>

    <form method="post" action="../controller/sign_in_process.php" class="p-4" style="border-radius: 25px;">
      <div class="mb-3">
        <label for="signinEmail" class="form-label">Email address</label>
        <input type="email" class="form-control" id="signinEmail" name="email" required autofocus>
      </div>
      <div class="mb-3">
        <label for="signinPassword" class="form-label">Password</label>
        <input type="password" class="form-control" id="signinPassword" name="password" required>
      </div>
      <button class="btn btn-foodplaza w-100 py-2 mt-2" type="submit">Sign In</button>
    </form>
    <div class="text-center mt-4">
      <span class="text-white">Don't have an account?</span>
      <a href="./signup.php" class="signin-link ms-1">Sign up</a>
    </div>
  </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Check if login failed
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('login') === 'error') {
    Swal.fire({
        icon: 'error',
        title: 'Login Failed!',
        text: 'Invalid email or password. Please try again.',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Try Again'
    }).then((result) => {
        // Clean the URL by removing the login parameter
        const url = new URL(window.location);
        url.searchParams.delete('login');
        window.history.replaceState({}, document.title, url.pathname + url.search);
    });
}
</script>

</html>