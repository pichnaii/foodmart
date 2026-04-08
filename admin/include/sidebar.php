<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";     
    $dbname = "foodmart";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // $config = "SELECT * FROM configurations";
    // $config_result = $conn->query($config);
    // $config_page = $config_result->fetch_assoc();
    $config_page = $conn->query('SELECT * FROM configurations')->fetch_assoc();
    $conn->close();
?>
<style>
    :root {
        --primary: <?= $config_page['sidebar_color'] ?> !important;
        --bg-content: <?php echo ($config_page['theme_color'] == 1) ? '#FFFFFF' : '#090909'; ?> !important;
        --bg-color: <?php echo ($config_page['theme_color'] == 1) ? '#F3F6F9' : '#343434'; ?> !important;
        --text-color: <?php echo ($config_page['theme_color'] == 1) ? '#343434' : '#343434'; ?> !important;
        --text-title: <?php echo ($config_page['theme_color'] == 1) ? '#000000' : '#FFFFFF'; ?> !important;
        --bg-type-color : <?php echo ($config_page['theme_color'] == 1) ? '#e9ecef' : '#090909'; ?> !important;
        --bg-type-selected: <?php echo ($config_page['theme_color'] == 1) ? '#FFFFFF' : '#090909'; ?> !important;
        --body-text: <?php echo ($config_page['theme_color'] == 1) ? '#343434' : '#CACACA'; ?> !important;
        --light: #F3F6F9;
        --white: #ffffff;
        --dark: <?php echo ($config_page['theme_color'] == 1) ? '#191C24' : '#FFFFFF'; ?> !important;;
    }
</style>
<!-- Sidebar Start -->
<div class="sidebar bg-light pe-0 pb-0">
    <nav class="navbar bg-light navbar-light">
        <a href="index.php" class="navbar-brand mx-4 mb-0">
            <h3 class="text-primary">
                <img class="rounded-circle me-2" src="img/logo1.png" alt="" style="width: 50px; height: 50px;">A&W Store
            </h3>
        </a>
        <div class="navbar-nav w-100">
            <a href="index.php" class="nav-item nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <?php if ($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_page['slideshow'] == 1)) { ?>
                <a href="slideshow.php" class="nav-item nav-link <?php echo $current_page == 'slideshow.php' ? 'active' : ''; ?>">
                    <i class="fa fa-ellipsis-h me-2"></i>Slideshow
                </a>
            <?php } if ($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_page['product'] == 1)) { ?>
                <a href="product.php" class="nav-item nav-link <?php echo $current_page == 'product.php' ? 'active' : ''; ?>">
                    <i class="fa fa-database me-2"></i>Product
                </a>
            <?php } if ($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_page['product'] == 1)) { ?>
                <a href="purchase.php" class="nav-item nav-link <?php echo $current_page == 'purchase.php' ? 'active' : ''; ?>">
                    <i class="fa fa-money-bill-wave me-2"></i>Purchase
                </a>
            <?php } if ($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_page['user'] == 1)) { ?>
                <a href="user.php" class="nav-item nav-link <?php echo $current_page == 'user.php' ? 'active' : ''; ?>">
                    <i class="fa fa-user-circle me-2"></i>User
                </a>
                <a href="user_permission.php" class="nav-item nav-link <?php echo $current_page == 'user_permission.php' ? 'active' : ''; ?>">
                    <i class="fa fa-user-circle me-2"></i>User Permission
                </a>
            <?php } if ($_SESSION['user_role'] == 'admin') { ?>
                <a href="configuration.php" class="nav-item nav-link <?php echo $current_page == 'configuration.php' ? 'active' : ''; ?>">
                    <i class="fa fa-wrench me-2"></i>Configuration
                </a>
            <?php } if ($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_page['setting'] == 1)) { ?>
                <a href="setting.php" class="nav-item nav-link <?php echo $current_page == 'setting.php' ? 'active' : ''; ?>">
                    <i class="fa fa-cog me-2"></i>Setting
                </a>
            <?php } if ($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_page['report'] == 1)) { ?>
                <!-- <a href="#reportSubmenu" class="nav-item nav-link <?php echo in_array($current_page, ['sale_report.php', 'pos_sale_report.php']) ? 'active' : ''; ?>" data-bs-toggle="collapse">
                    <i class="fa fa-edit me-2"></i>Report
                </a> -->
                <a href="#reportSubmenu" class="nav-item nav-link <?php echo in_array($current_page, ['sale_report.php', 'pos_sale_report.php']) ? 'active' : ''; ?>" data-bs-toggle="collapse" data-bs-target="#reportSubmenu" aria-expanded="<?php echo in_array($current_page, ['sale_report.php', 'pos_sale_report.php']) ? 'true' : 'false'; ?>" aria-controls="reportSubmenu">
                    <i class="fa fa-save me-2"></i>Report
                </a>
                <div id="reportSubmenu" class="collapse <?php echo in_array($current_page, ['sale_report.php', 'pos_sale_report.php']) ? 'show' : ''; ?>">
                    <a href="sale_report.php" class="nav-item nav-link ms-4 <?php echo $current_page == 'sale_report.php' ? 'active' : ''; ?>">
                        <i class="fa fa-file-alt me-2"></i>Sale Report
                    </a>
                    <a href="pos_sale_report.php" class="nav-item nav-link ms-4 <?php echo $current_page == 'pos_sale_report.php' ? 'active' : ''; ?>">
                        <i class="fa fa-file-invoice me-2"></i>POS Sale Report
                    </a>
                </div>
            <?php } ?>
            <a href="logout.php" class="nav-item nav-link text-danger">
                <i class="fa fa-sign-out-alt me-2 text-danger"></i>Logout
            </a>
        </div>
    </nav>
</div>
<!-- Sidebar End -->