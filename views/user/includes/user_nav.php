<?php

$current = basename($_SERVER['PHP_SELF']);
?>


<div class="col-md-3 col-lg-3 sidebar" style="height: 100vh;">
                    <div class="brand">
                        <!-- LaFlora <span class="brand-icon">❀</span> -->
                      <a class="navbar-brand" href="../../index.php">LaFlora <i class="fa-solid fa-seedling ms-1"></i></a>
                    </div>
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-name"><?php echo $_SESSION['name']; ?></div>
                        <div class="user-email"><?php echo $_SESSION['email']; ?></div>
                    </div>
                    
                    <div class="mt-4">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?php if($current=='profile.php') echo ' active'; ?>" href="./profile.php"><i class="fas fa-user nav-icon"></i> My Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if($current=='orders.php') echo ' active'; ?>" href="./orders.php"><i class="fas fa-box nav-icon"></i> My Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if($current=='wishlist.php') echo ' active'; ?>" href="./wishlist.php"><i class="fas fa-heart nav-icon"></i> Wishlist</a>
                            </li>
                            <form action="../../controller/user_logout_process.php" method="post">
                                <input type="hidden" name="logout" value="true">
                                <li class="nav-item">
                                    <button type="submit" class="nav-link logout-btn"><i class="fas fa-sign-out-alt nav-icon"></i> Logout</button>
                                </li>
                            </form>
                        </ul>
                    </div>
 </div>