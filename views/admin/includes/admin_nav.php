<?php

$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="col-md-3 col-lg-2 d-md-block admin-sidebar sidebar p-0">
                <div class="position-sticky d-flex flex-column align-items-center py-4">
                    <a class="navbar-brand mb-4 admin-brand" href="../../index.php">
                        LaFlora <i class="fa-solid fa-seedling ms-1"></i>
                    </a>
                    <ul class="nav flex-column w-100">
                        <li class="nav-item mb-2">
                            <a class="nav-link admin-nav-link<?php if($current=='dashboard.php') echo ' active'; ?>" href="./dashboard.php"><i class="fas fa-tachometer-alt nav-icon me-2"></i> Dashboard</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link admin-nav-link<?php if($current=='products.php') echo ' active'; ?>" href="./products.php"><i class="fas fa-box-open nav-icon me-2"></i> Products</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link admin-nav-link<?php if($current=='categories.php') echo ' active'; ?>" href="./categories.php"><i class="fas fa-tags nav-icon me-2"></i> Categories</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link admin-nav-link<?php if($current=='orders.php') echo ' active'; ?>" href="./orders.php"><i class="fas fa-shopping-cart nav-icon me-2"></i> Orders</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link admin-nav-link<?php if($current=='feedbacks.php') echo ' active'; ?>" href="./feedbacks.php"><i class="fas fa-comments nav-icon me-2"></i> Feedbacks</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link admin-nav-link<?php if($current=='users.php') echo ' active'; ?>" href="./users.php"><i class="fas fa-users nav-icon me-2"></i> Users</a>
                        </li>
                       <form action="../../controller/user_logout_process.php" method="post">
                                <input type="hidden" name="logout" value="true">
                                <li class="nav-item mb-2">
                                    <button type="submit" class="nav-link admin-nav-link"><i class="fas fa-sign-out-alt nav-icon me-2"></i> Logout</button>
                                </li>
                            </form>
                    </ul>
                </div>
            </nav>