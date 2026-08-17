<?php 
    require_once 'include/dbconnection.php';
    $isAdmin    = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
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
        <div class="content">
            <?php include "include/navbar.php"?>
            <div class="container-fluid pt-3 px-3">
                <div class="bg-light rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="mb-0 fw-bold text-title fs-3">Reports <i class="bi bi-receipt-cutoff"></i></h5>
                    </div>
                    <div class="row g-3">
                        <h3 class="m-0">Purchase Reports</h3>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <a href="purchase_reports.php" class="fs-5"><i class="bi bi-file-earmark-text-fill pe-1"></i>Purchase Report</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <a href="purchase_details_reports.php" class="fs-5"><i class="bi bi-file-earmark-text pe-1"></i>Purchase Details Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "include/footer.php"?>
		</div>
		<?php include "include/foot.php"?>
	</div>
</body>
</html>