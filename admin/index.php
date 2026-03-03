<?php
    require_once 'include/dbconnection.php';
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $totalProducts = "SELECT 
                        COUNT(*) as count,
                        SUM(cost * quantity) as cost, 
                        SUM(price * quantity) as price 
                        FROM products 
                        WHERE status != 0
                    ";
    $totalProduct = $conn->query($totalProducts)->fetch_assoc();

    // get purchase today
    $purchasestodays = "SELECT 
                        SUM(grand_total) as grand_total 
                        FROM purchases 
                        WHERE DATE(create_date) = CURDATE()
                    ";
    $purchasetoday = $conn->query($purchasestodays)->fetch_assoc();

    // get purchase this month
    $purchasesthismonths = "SELECT SUM(grand_total) AS grand_total
                            FROM purchases
                            WHERE DATE(create_date) BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND CURDATE()
                        ";
    $purchasesthismonth = $conn->query($purchasesthismonths)->fetch_assoc();

    // get purchase last month
    $purchaseslastmonths = " SELECT SUM(grand_total) AS grand_total
                                FROM purchases
                                WHERE DATE(create_date) BETWEEN 
                                    DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                                    AND LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            ";
    $purchaseslastmonth = $conn->query($purchaseslastmonths)->fetch_assoc();

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php include "include/header.php"?>
<body>
    <div class="position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->
		<?php include "include/sidebar.php"?>
		<?php include "dashboard.php"?>
		<?php include "include/foot.php"?>
	</div>
</body>
</html>