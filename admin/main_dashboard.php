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
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="sale-tab" data-bs-toggle="pill" data-bs-target="#sale" type="button" role="tab" aria-controls="sale" aria-selected="false">Sale Dashboard</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="purchase-tab" data-bs-toggle="pill" data-bs-target="#purchase" type="button" role="tab" aria-controls="purchase" aria-selected="false">Purchase Dashboard</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="expense-tab" data-bs-toggle="pill" data-bs-target="#expense" type="button" role="tab" aria-controls="expense" aria-selected="false">Expense Dashboard</button>
                                    </li>
                                </ul>
                                <h5 class="mb-0 fw-bold text-title fs-3">Dashboard <i class="fa fa-tachometer-alt"></i></h5>
                            </div>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                                    overview
                                </div>
                                <div class="tab-pane fade" id="sale" role="tabpanel" aria-labelledby="sale-tab">
                                    sale
                                </div>
                                <div class="tab-pane fade" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
                                    purchase
                                </div>
                                <div class="tab-pane fade" id="expense" role="tabpanel" aria-labelledby="expense-tab">
                                    Expense
                                </div>
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