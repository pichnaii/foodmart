<?php require_once 'include/dbconnection.php'; ?>
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
                        <h5 class="mb-0 fw-bold text-title fs-3">Settings <i class="fa fa-cog"></i></h5>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="my-2">
                                <a href="customer.php" class="fs-5"><i class="bi bi-people-fill pe-1"></i>Customer</a>
                            </div>
                            <div class="my-2">
                                <a href="supplier.php" class="fs-5"><i class="bi bi-person-circle pe-1"></i>Supplier</a>
                            </div>
                            <div class="my-2">
                                <a href="warehouse.php" class="fs-5"><i class="bi bi-house-door-fill pe-1"></i>Warehouse</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="my-2">
                                <a href="unit.php" class="fs-5"><i class="bi bi-box-seam pe-1"></i>Unit</a>
                            </div>
                            <div class="my-2">
                                <a href="category.php" class="fs-5"><i class="fa fa-th-large pe-1"></i>Category</a>
                            </div>
                            <div class="my-2">
                                <a href="currency.php" class="fs-5"><i class="fa fa-dollar-sign pe-1"></i>Currency</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="my-2">
                                <a href="company.php" class="fs-5"><i class="fa fa-building pe-1"></i>Company</a>
                            </div>
                            <div class="my-2">
                                <a href="permission.php" class="fs-5"><i class="bi bi-person-circle pe-1"></i>Supplier</a>
                            </div>
                            <div class="my-2">
                                <a href="#" class="fs-5"><i class="bi bi-house-door-fill pe-1"></i>Warehouse</a>
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