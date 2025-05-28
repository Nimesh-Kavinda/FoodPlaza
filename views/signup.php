<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Food Plaza</title>
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
<body class="signup-page">
  <div class="signup-card">
       <a href="../index.html" style="text-decoration: none;"> <div class="signup-logo" style="color: #fff !important;">Food Plaza <i class="fa-solid fa-utensils"></i></div> </a>
    <div class="signup-title">Create Your Account</div>
    <form method="post" action="../controller/sign_up_process.php" class="p-4" style="border-radius: 25px;">
      <div class="mb-3">
        <label for="signupName" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="signupName" name="name" required autofocus>
      </div>
      <div class="mb-3">
        <label for="signupEmail" class="form-label">Email address</label>
        <input type="email" class="form-control" id="signupEmail" name="email" required>
      </div>
      <div class="mb-3">
        <label for="signupPassword" class="form-label">Password</label>
        <input type="password" class="form-control" id="signupPassword" name="password" required>
      </div>
      <div class="mb-3">
        <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="signupConfirmPassword" name="confirmPassword" required>
      </div>
      <button class="btn btn-foodplaza w-100 py-2 mt-2" type="submit">Sign Up</button>
    </form>
    <div class="text-center mt-3">
      <span class="text-white">Already have an account?</span>
      <a href="./signin.php" class="signup-link ms-1">Sign in</a>
    </div>
  </div>
</body>
</html>
