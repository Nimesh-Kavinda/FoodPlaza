<?php
session_start();
include './config/db.php';

$produtcts = [];
$stmt = $conn->query('SELECT id, name, price, image FROM products ORDER BY id DESC LIMIT 4');
if ($stmt) {
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$feedbacks = [];
$stmt = $conn->prepare('
    SELECT f.*, u.name AS user_name
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.id
    WHERE f.status = "active"
    ORDER BY f.f_id DESC
    LIMIT 12
');
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Plaza - Online Food Ordering</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts for Logo -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <!-- Google Fonts for body and headings -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Montserrat:wght@400;600&family=Quicksand:wght@400;600&display=swap" rel="stylesheet"> <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="public/css/main.css">

</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
    <div class="container">
      <a class="navbar-brand" href="#">Food Plaza <i class="fa-solid fa-utensils ms-1"></i></a>     
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#home">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./views/about.php">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./views/shop.php">Shop</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./views/contact.php">Contact</a>
          </li>
        </ul>
        <ul class="navbar-nav ms-3">
          <li class="nav-item">
            <a class="nav-link <?php if ($current == 'wishlist.php') echo 'active'; ?> position-relative" href="./views/wishlist.php" title="Wishlist">
              <i class="fa-regular fa-heart"></i>
              <?php
              if (isset($_SESSION['user_id'])) {
                $stmt = $conn->prepare('SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $count = (int)($result['count'] ?? 0);
                if ($count > 0) {
                  echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger wishlist-count">'
                    . $count . '</span>';
                } else {
                  echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger wishlist-count" style="display: none;">0</span>';
                }
              }
              ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if ($current == 'cart.php') echo 'active'; ?> position-relative" href="./views/cart.php" title="Cart">
              <i class="fa-solid fa-cart-shopping"></i>
              <?php
              if (isset($_SESSION['user_id'])) {
                $stmt = $conn->prepare('SELECT COUNT(*) as count FROM cart WHERE user_id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $count = (int)($result['count'] ?? 0);
                echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count"'
                  . ($count > 0 ? '' : ' style="display:none;"') . '>' . $count . '</span>';
              } else {
                echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" style="display:none;">0</span>';
              }
              ?>
            </a>
          </li>
          <?php
          if (isset($_SESSION['name'])) {
            if ($_SESSION['user_type'] == 'admin') {
              echo '<li class="nav-item">
                            <a class="nav-link" href="./views/admin/dashboard.php" title="Admin Dashboard"><i class="fa-solid fa-user-shield"></i></a>                            
                          </li>';
            } else
              echo '<li class="nav-item">
                        <a class="nav-link" href="./views/user/profile.php" title="Profile"><i class="fa-regular fa-user"></i></a>
                      </li>';
          } else {
            echo '<li class="nav-item">
                        <a class="nav-link" href="views/signin.php" title="Sign In"><i class="fa-regular fa-user"></i></a>
                      </li>';
          }
          ?>
           <?php if (isset($_SESSION['name'])) { ?>
           <li class="nav-link username"><?php echo htmlspecialchars($_SESSION['name']); ?></li>
         <?php } ?> 
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section" id="home">
    <div class="container">
      <h1>Welcome to Food Plaza</h1>
      <p class="mb-4">Order the tastiest, freshest food online. Fast delivery, delicious meals, and a flavor for every craving.</p>
      <a href="./views/shop.php" class="btn btn-foodplaza btn-lg">Order Now</a>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
          <h2 class="mb-3" style="color:var(--foodplaza-primary)">Why Choose Food Plaza?</h2>
          <p>Food Plaza brings you handpicked, premium meals delivered right to your doorstep. Our expert chefs craft each dish with love and care, ensuring every order is a memorable experience. Whether it's a birthday, anniversary, or just because, we have the perfect meal for you.</p>
          <ul class="list-unstyled mt-3">
            <li><i class="fa-solid fa-truck-fast me-2" style="color:var(--foodplaza-secondary)"></i>Same-day delivery available</li>
            <li><i class="fa-solid fa-leaf me-2" style="color:var(--foodplaza-secondary)"></i>Eco-friendly packaging</li>
            <li><i class="fa-solid fa-award me-2" style="color:var(--foodplaza-secondary)"></i>100% freshness guarantee</li>
          </ul>
        </div>
        <div class="col-md-6 text-center">
          <div class="image-slider-container" id="about-image-slider">
            <img src="./public/img/home/flower_banner_01.jpg" class="image-slider-slide active" alt="Bouquet 1">
            <img src="./public/img/home/flower_banner_02.jpg" class="image-slider-slide" alt="Bouquet 2">
            <img src="./public/img/home/flower_banner_03.jpg" class="image-slider-slide" alt="Bouquet 3">
            <img src="./public/img/home/flower_banner_04.jpg" class="image-slider-slide" alt="Bouquet 4">
            <div class="image-slider-dots">
              <span class="image-slider-dot active" data-slide="0"></span>
              <span class="image-slider-dot" data-slide="1"></span>
              <span class="image-slider-dot" data-slide="2"></span>
              <span class="image-slider-dot" data-slide="3"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Products Section -->
  <section id="products" class="py-5">
    <div class="container">
      <h2 class="text-center mb-5" style="color:var(--foodplaza-primary)">Our Best Dishes</h2>
      <div class="row g-4">
        <!-- Product Card Example -->

        <?php foreach ($products as $product): ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card product-card h-75 shadow-lg border-0">
              <img src="./uploads/products/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
              <div class="card-body text-center">
                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                <p class="card-text">Rs. <?php echo number_format($product['price'], 2); ?></p>
                <?php if (isset($_SESSION['user_id'])): ?>
                  <button type="button" class="btn btn-foodplaza add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                    <i class="fa fa-cart-plus me-1"></i> Add to Cart
                  </button>
                <?php else: ?>
                  <a href="views/signin.php" class="btn btn-foodplaza">
                    <i class="fa fa-cart-plus me-1"></i> Add to Cart
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <!-- Add more product cards as needed -->
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="testimonials" class="testimonial-section">
    <div class="container">
      <div class="section-title">
        <h2>What Our Customers Say</h2>
        <p>Discover why thousands of customers love our floral arrangements and exceptional service</p>
      </div>

      <div class="testimonial-slider">
          <?php if (!$feedbacks) {
           echo '<p class="text-center">No testimonials available at the moment.</p>';
          } else { ?>
        <div class="testimonial-track" id="testimonialTrack">
          <!-- Testimonial 1 -->
        <?php
            foreach ($feedbacks as $feedback): ?>
              <div class="testimonial-slide">
                <div class="testimonial-card">
                 
                  <p class="testimonial-message">
                    "<?php echo htmlspecialchars($feedback['message']); ?>"
                  </p>
                  <h5 class="testimonial-author"><?php echo htmlspecialchars($feedback['user_name']); ?></h5>
                </div>
              </div>
          <?php endforeach;
          } ?>          

        </div>
      </div>

      <div class="slider-nav">
        <button class="slider-btn" id="prevBtn" onclick="changeSlide(-1)">
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="slider-btn" id="nextBtn" onclick="changeSlide(1)">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>

      <div class="slider-dots" id="sliderDots"></div>
    </div>
  </section>


  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row align-items-stretch gy-4">
        <div class="col-md-4 d-flex align-items-center justify-content-center justify-content-md-start">
          <div class="footer-section">
            <span class="footer-logo">Food Plaza <i class="fa-solid fa-utensils"></i></span>
            <div>Order the tastiest food online.</div>
          </div>
        </div>
        <div class="col-md-1 d-none d-md-flex align-items-center">
          <div class="footer-divider"></div>
        </div>
        <div class="col-md-3 d-flex align-items-center justify-content-center">
          <div class="footer-section text-center text-md-start">
            <div class="fw-bold mb-1">Quick Links</div>
           <a href="../index.php" class="footer-link">Home</a>
          <a href="../views/about.php" class="footer-link">About</a>
          <a href="../views/shop.php" class="footer-link">Shop</a>
          <a href="../views/contact.php" class="footer-link">Contact</a>
          </div>
        </div>
        <div class="col-md-1 d-none d-md-flex align-items-center">
          <div class="footer-divider"></div>
        </div>
        <div class="col-md-3 d-flex align-items-center justify-content-center justify-content-md-end">
          <div class="footer-section text-center text-md-end">
            <div class="fw-bold mb-1">Contact & Social</div>
            <div class="mb-1">
              <a href="#" class="footer-social" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="footer-social" title="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="#" class="footer-social" title="Twitter"><i class="fab fa-twitter"></i></a>
            </div>
            <div>Call: <a href="tel:0715343747" class="footer-link">0715343747</a></div>
            <div>Email: <a href="mailto:info@foodplaza.com" class="footer-link">info@foodplaza.com</a></div>
          </div>
        </div>
      </div>
      <div class="footer-bottom mt-4">&copy; 2025 Food Plaza. All rights reserved.</div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
  <!-- Custom JS -->
  <script src="public/js/main.js"></script>
  <script src="public/js/cart.js"></script>

  <script>
    let currentSlide = 0;
    const track = document.getElementById('testimonialTrack');
    const slides = document.querySelectorAll('.testimonial-slide');
    const totalSlides = slides.length;

    // Get slides per view based on screen size
    function getSlidesPerView() {
      if (window.innerWidth >= 992) return 3;
      if (window.innerWidth >= 768) return 2;
      return 1;
    }

    let slidesPerView = getSlidesPerView();
    const maxSlide = Math.max(0, totalSlides - slidesPerView);

    // Create dots
    function createDots() {
      const dotsContainer = document.getElementById('sliderDots');
      dotsContainer.innerHTML = '';

      for (let i = 0; i <= maxSlide; i++) {
        const dot = document.createElement('span');
        dot.className = 'dot';
        if (i === 0) dot.classList.add('active');
        dot.onclick = () => goToSlide(i);
        dotsContainer.appendChild(dot);
      }
    }

    // Update slider position
    function updateSlider() {
      const slideWidth = 100 / slidesPerView;
      const translateX = -currentSlide * slideWidth;
      track.style.transform = `translateX(${translateX}%)`;

      // Update dots
      document.querySelectorAll('.dot').forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
      });

      // Update button states
      document.getElementById('prevBtn').disabled = currentSlide === 0;
      document.getElementById('nextBtn').disabled = currentSlide >= maxSlide;
    }

    // Change slide function
    function changeSlide(direction) {
      currentSlide += direction;

      if (currentSlide < 0) currentSlide = 0;
      if (currentSlide > maxSlide) currentSlide = maxSlide;

      updateSlider();
    }

    // Go to specific slide
    function goToSlide(slideIndex) {
      currentSlide = slideIndex;
      updateSlider();
    }

    // Auto slide functionality
    let autoSlideInterval;

    function startAutoSlide() {
      autoSlideInterval = setInterval(() => {
        if (currentSlide >= maxSlide) {
          currentSlide = 0;
        } else {
          currentSlide++;
        }
        updateSlider();
      }, 4000);
    }

    function stopAutoSlide() {
      clearInterval(autoSlideInterval);
    }

    // Handle window resize
    window.addEventListener('resize', () => {
      slidesPerView = getSlidesPerView();
      const newMaxSlide = Math.max(0, totalSlides - slidesPerView);

      if (currentSlide > newMaxSlide) {
        currentSlide = newMaxSlide;
      }

      createDots();
      updateSlider();
    });

    // Touch/swipe support
    let startX = 0;
    let isDragging = false;

    track.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      isDragging = true;
      stopAutoSlide();
    });

    track.addEventListener('touchmove', (e) => {
      if (!isDragging) return;
      e.preventDefault();
    });

    track.addEventListener('touchend', (e) => {
      if (!isDragging) return;

      const endX = e.changedTouches[0].clientX;
      const diff = startX - endX;

      if (Math.abs(diff) > 50) {
        if (diff > 0) {
          changeSlide(1);
        } else {
          changeSlide(-1);
        }
      }

      isDragging = false;
      startAutoSlide();
    });

    // Mouse events for desktop
    track.addEventListener('mouseenter', stopAutoSlide);
    track.addEventListener('mouseleave', startAutoSlide);

    // Initialize
    createDots();
    updateSlider();
    startAutoSlide();

const urlParams = new URLSearchParams(window.location.search);

// Check if login was successful
if (urlParams.get('login') === 'success') {
    Swal.fire({
        icon: 'success',
        title: 'Welcome!',
        text: 'You have successfully logged in.',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    }).then((result) => {
        // Clean the URL by removing the login parameter
        const url = new URL(window.location);
        url.searchParams.delete('login');
        window.history.replaceState({}, document.title, url.pathname + url.search);
    });
}

// Check if logout was successful
if (urlParams.get('logout') === 'success') {
    Swal.fire({
        icon: 'success',
        title: 'Goodbye!',
        text: 'You have been successfully logged out.',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    }).then((result) => {
        // Clean the URL by removing the logout parameter
        const url = new URL(window.location);
        url.searchParams.delete('logout');
        window.history.replaceState({}, document.title, url.pathname + url.search);
    });
}

  </script>

</body>

</html>