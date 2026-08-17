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

    // permission flags are populated in include/dbconnection.php
    $isAdmin    = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
    $isProductDisplay = isset($_SESSION['product_display']) && $_SESSION['product_display'] == 1;
    $isReportDisplay = isset($_SESSION['report_display']) && $_SESSION['report_display'] == 1;

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
            <!-- <a href="index.php" class="nav-item nav-link <?php echo $current_page == 'index.php' || $current_page == 'dashboard_sales.php' ? 'active' : ''; ?>">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a> -->
            <a href="main_dashboard.php" class="nav-item nav-link <?php echo $current_page == 'index.php' || $current_page == 'dashboard_sales.php' ? 'active' : ''; ?>">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <?php if (1 + 1 !== 2) { ?>
                <a href="slideshow.php" class="nav-item nav-link <?php echo $current_page == 'slideshow.php' ? 'active' : ''; ?>">
                    <i class="fa fa-ellipsis-h me-2"></i>Slideshow
                </a>
            <?php }  ?>
            <?php if ($isAdmin || $isProductDisplay) { ?>
                <a href="product.php" class="nav-item nav-link <?php echo $current_page == 'product.php' ? 'active' : ''; ?>">
                    <i class="fa fa-database me-2"></i>Product
                </a>
            <?php } ?>
            <a href="sale.php" class="nav-item nav-link <?php echo $current_page == 'sale.php' ? 'active' : ''; ?>">
                <i class="fa fa-dollar-sign me-2"></i>Sale
            </a>
            <a href="purchase.php" class="nav-item nav-link <?php echo $current_page == 'purchase.php' ? 'active' : ''; ?>">
                <i class="fa fa-money-bill-wave me-2"></i>Purchase
            </a>
            <a href="expense.php" class="nav-item nav-link <?php echo $current_page == 'expense.php' ? 'active' : ''; ?>">
                <i class="bi bi-coin me-2"></i>Expense
            </a>
            <a href="setting.php" class="nav-item nav-link <?php echo $current_page == 'setting.php' ? 'active' : ''; ?>">
                <i class="fa fa-cog me-2"></i>Setting
            </a>
            <?php if ($isAdmin || $isReportDisplay) { ?>
                <a href="reports.php" class="nav-item nav-link 
                    <?= (
                            $current_page == 'report.php' || 
                            $current_page == 'purchase_reports.php' || 
                            $current_page == 'purchase_detail_reports.php'
                        ) 
                        ? 'active' : ''; 
                    ?> ">
                    <i class="fa fa-edit me-2"></i>Report
                </a>
            <?php } ?>
            <a href="logout.php" class="nav-item nav-link text-danger">
                <i class="fa fa-sign-out-alt me-2 text-danger"></i>Logout
            </a>
        </div>
    </nav>
</div>
<!-- Sidebar End -->