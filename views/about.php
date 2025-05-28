<?php
    session_start();
    include '../config/db.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Food Plaza</title>
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
<body>
<!-- Navbar (copied from index.html, do not change) -->
<?php include_once('../includes/nav.php'); ?>

<section class="about-hero-section py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h1 class="about-title mb-3">About Food Plaza</h1>
        <p class="about-desc mb-4">
          Welcome to Food Plaza, your premier destination for delicious meals and heartfelt service. Founded with a passion for spreading joy and flavor, Food Plaza has become a trusted name for online food delivery. Our journey began with a simple belief: every occasion deserves to be celebrated with the tastiest, freshest food.<br><br>
          At Food Plaza, we source our ingredients directly from sustainable suppliers, ensuring every dish is bursting with flavor and quality. Our talented chefs handcraft each meal with meticulous attention to detail, blending creativity and care to make every order unique. Whether you’re celebrating a birthday, anniversary, or simply want to enjoy a great meal, we have the perfect dish for you.<br><br>
          We are committed to eco-friendly practices, using recyclable packaging and supporting local growers. Our same-day delivery service ensures your food arrives fresh and on time, every time. With a focus on customer satisfaction, we go the extra mile to make your experience seamless and memorable. Discover the Food Plaza difference—where every bite tells a story of love, joy, and connection.
        </p>
        <ul class="about-features list-unstyled mb-4">
          <li><i class="fa-solid fa-truck-fast me-2" style="color:var(--foodplaza-secondary)"></i>Fast & scheduled delivery</li>
          <li><i class="fa-solid fa-leaf me-2" style="color:var(--foodplaza-secondary)"></i>Eco-friendly, recyclable packaging</li>
          <li><i class="fa-solid fa-award me-2" style="color:var(--foodplaza-secondary)"></i>100% freshness & satisfaction guarantee</li>
          <li><i class="fa-solid fa-people-group me-2" style="color:var(--foodplaza-secondary)"></i>Family-owned, community-focused</li>
          <li><i class="fa-solid fa-gift me-2" style="color:var(--foodplaza-secondary)"></i>Custom meal combos</li>
        </ul>
        <a href="./shop.php" class="btn btn-foodplaza btn-lg">Order Food</a>
      </div>
      <div class="col-lg-6 d-flex align-items-center justify-content-center">
        <div class="about-img-wrapper about-img-single-wrapper w-100">
          <img src="../public/img/about/about_cover.jpg" alt="Food Plaza" class="about-img about-img-animate about-img-single">
        </div>
      </div>
    </div>
    <div class="row mt-5 g-4">
      <div class="col-md-4">
        <div class="about-highlight-card text-center p-4 h-100">
          <i class="fa-solid fa-utensils fa-2x mb-3" style="color:var(--foodplaza-primary)"></i>
          <h5 class="mb-2">Fresh Ingredients</h5>
          <p class="mb-0">We partner with local farmers and use eco-friendly packaging to protect the planet with every delivery.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="about-highlight-card text-center p-4 h-100">
          <i class="fa-solid fa-heart fa-2x mb-3" style="color:var(--foodplaza-primary)"></i>
          <h5 class="mb-2">Made With Love</h5>
          <p class="mb-0">Our chefs pour creativity and care into every meal, making each dish truly special.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="about-highlight-card text-center p-4 h-100">
          <i class="fa-solid fa-gift fa-2x mb-3" style="color:var(--foodplaza-primary)"></i>
          <h5 class="mb-2">Perfect for Every Occasion</h5>
          <p class="mb-0">From family dinners to festive celebrations, we offer custom meal solutions to make every moment memorable.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonial Section -->
<section class="testimonial-section py-5">
  <div class="container">
    <h2 class="text-center mb-5 testimonial-title">Our Core Values</h2>
    <div class="row justify-content-center g-4">
      <div class="col-md-4">
        <div class="testimonial-card p-4 h-100 text-center">
          <div class="mb-3">
            <i class="fas fa-leaf fa-3x text-success"></i>
          </div>
          <h4 class="testimonial-name mb-3">Sustainability</h4>
          <p class="testimonial-text">We are committed to eco-friendly practices, from sourcing locally grown ingredients to using biodegradable packaging materials for all our deliveries.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card p-4 h-100 text-center">
          <div class="mb-3">
            <i class="fas fa-heart fa-3x text-danger"></i>
          </div>
          <h4 class="testimonial-name mb-3">Quality & Care</h4>
          <p class="testimonial-text">Every meal is handcrafted with love and attention to detail. We ensure only the freshest, most delicious ingredients reach your table.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card p-4 h-100 text-center">
          <div class="mb-3">
            <i class="fas fa-users fa-3x text-primary"></i>
          </div>
          <h4 class="testimonial-name mb-3">Community First</h4>
          <p class="testimonial-text">As a family-owned business, we believe in supporting our local community and building lasting relationships with our customers and suppliers.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer (copied from index.html, do not change) -->
<?php include_once('../includes/footer.php'); ?>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="../public/js/main.js"></script>
</body>
</html>
