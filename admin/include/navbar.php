<!-- start navbar -->
<nav class="navbar navbar-expand bg-light navbar-dark sticky-top px-4 py-0">
    <a href="index.php" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0">
            <img class="rounded-circle me-2" src="img/logo1.png" alt="" style="width: 50px; height: 50px;">A&W
        </h2>
    </a>
    <a href="#" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars text-dark"></i>
    </a>
    <form class="d-none d-md-flex ms-4">
        <input class="form-control border-0 text-primary" type="search" placeholder="Search...">
    </form>
    <div class="navbar-nav align-items-center ms-auto text-dark">
        <?php if ($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_page['user'] == 1)) { ?>    
            <div>
                <a href="pos.php" class="nav-link px-3 dark" target="_blank">
                    <i class="fa fa-shopping-cart me-lg-2 text-dark"></i>
                    <span class="d-none d-lg-inline-flex">POS</span>
                </a>
            </div>
        <?php } ?>
        <div>
            <a href="#" class="nav-link px-3 dark">
                <i class="fa fa-<?php echo ($config_page['theme_color'] == 1) ? 'sun' : 'moon'; ?> me-lg-2 text-dark"></i>
                <span class="d-none d-lg-inline-flex"><?php echo ($config_page['theme_color'] == 1) ? 'Light' : 'Dark'; ?></span>
            </a>
        </div>
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa fa-envelope me-lg-2 text-dark"></i>
                <span class="d-none d-lg-inline-flex">Message</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="#" class="dropdown-item">
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle" src="img/panda.png" alt="" style="width: 40px; height: 40px;">
                        <div class="ms-2">
                            <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                            <small>15 minutes ago</small>
                        </div>
                    </div>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle" src="img/panda.png" alt="" style="width: 40px; height: 40px;">
                        <div class="ms-2">
                            <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                            <small>15 minutes ago</small>
                        </div>
                    </div>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle" src="img/panda.png" alt="" style="width: 40px; height: 40px;">
                        <div class="ms-2">
                            <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                            <small>15 minutes ago</small>
                        </div>
                    </div>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item text-center">See all message</a>
            </div>
        </div>
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa fa-bell me-lg-2 text-dark"></i>
                <span class="d-none d-lg-inline-flex">Notificatin</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">Profile updated</h6>
                    <small>15 minutes ago</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">New user added</h6>
                    <small>15 minutes ago</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">
                    <h6 class="fw-normal mb-0">Password changed</h6>
                    <small>15 minutes ago</small>
                </a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item text-center">See all notifications</a>
            </div>
        </div>
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img class="rounded-circle me-lg-2" src="img/panda.png" alt="" style="width: 40px; height: 40px;">
                <span class="d-none d-lg-inline-flex fw-bold">
                    <?php 
                        if(isset($_SESSION['username'])) {
                            echo htmlspecialchars($_SESSION['username']);
                        } 
                    ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                <a href="user.php" class="dropdown-item">My Profile</a>
                <a href="#" class="dropdown-item">Settings</a>
                <a href="logout.php" class="dropdown-item"><i class="fa fa-sign-out-alt text-danger pe-2"></i>Log Out</a>
            </div>
        </div>
    </div>
</nav>
<!-- end navbar -->