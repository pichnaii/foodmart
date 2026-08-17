<!-- Content Start -->
<div class="content">
    <?php include "include/navbar.php"?>
    <div class="container-fluid pt-4 px-4">
        <?php if(isset($_SESSION['message'])){ ?>
            <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" id="message" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php } ?>
        <div class="row g-4">
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
                        <div class="container-fluid pt-0 px-0">
                            <div class="row g-4">
                                <div class="col-sm-6 col-xl-3">
                                    <div class="db_dashboard rounded d-flex align-items-center justify-content-between p-4">
                                        <i class="fa fa-chart-line fa-3x text-light"></i>
                                        <div class="ms-3">
                                            <p class="mb-2 text-light">Total Products</p>
                                            <h6 class="mb-0 text-light float-end fs-4"><?= $totalProduct['count'] ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <div class="db_card rounded d-flex align-items-center justify-content-between p-4">
                                        <i class="fa fa-chart-bar fa-3x text-light"></i>
                                        <div class="ms-3">
                                            <p class="mb-2 text-light">Total Cost Of Products</p>
                                            <h6 class="mb-0 text-light float-end fs-4"><?= format_currency($totalProduct['cost'], $currencyCode) ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <div class="db_acc rounded d-flex align-items-center justify-content-between p-4">
                                        <i class="fa fa-chart-area fa-3x text-light"></i>
                                        <div class="ms-3">
                                            <p class="mb-2 text-light">Today Price Of Products</p>
                                            <h6 class="mb-0 text-light float-end fs-4"><?= format_currency($totalProduct['price'], $currencyCode) ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <div class="db_box rounded d-flex align-items-center justify-content-between p-4">
                                        <i class="fa fa-chart-pie fa-3x text-light"></i>
                                        <div class="ms-3">
                                            <p class="mb-2 text-light">Total Revenue</p>
                                            <h6 class="mb-0 text-light fs-4"><?= format_currency($totalRevenue, $currencyCode) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sale & Revenue End -->

                        <!-- Sales Chart Start -->
                        <div class="container-fluid pt-4 px-0">
                            <div class="row g-4">
                                <div class="col-sm-12 col-xl-6">
                                    <div class="bg-light text-center rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0 text-title">Worldwide Sales</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <canvas id="worldwide-sales"></canvas>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xl-6">
                                    <div class="bg-light text-center rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0 text-title">Salse & Revenue</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <canvas id="salse-revenue"></canvas>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xl-6">
                                    <div class="bg-light text-center rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0 text-title">Salse & Revenue</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <canvas id="line-chart"></canvas>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xl-6">
                                    <div class="bg-light text-center rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0 text-title">Salse & Revenue</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <canvas id="bar-chart"></canvas>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xl-6">
                                    <div class="bg-light text-center rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0 text-title">Salse & Revenue</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <canvas id="pie-chart"></canvas>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xl-6">
                                    <div class="bg-light text-center rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0 text-title">Salse & Revenue</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <canvas id="doughnut-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sales Chart End -->

                        <!-- Recent Sales Start -->
                        <div class="container-fluid pt-4 px-0">
                            <div class="bg-light text-center rounded p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h6 class="mb-0">Recent Salse</h6>
                                    <a href="">Show All</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table text-start align-middle table-bordered table-hover mb-0 text-center">
                                        <thead>
                                            <tr class="text-dark">
                                                <th scope="col"><input class="form-check-input" type="checkbox"></th>
                                                <th scope="col">No</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Invoice</th>
                                                <th scope="col">Customer</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for($i=1; $i<=3; $i++) { ?>
                                                <tr>
                                                    <td><input class="form-check-input" type="checkbox"></td>
                                                    <td><?= $i ?></td>
                                                    <td>01 Jan 2045</td>
                                                    <td>INV-0123</td>
                                                    <td>Jhon Doe</td>
                                                    <td>$123</td>
                                                    <td><span class="btn btn-success">Paid</span></td>
                                                    <td>
                                                        <a class="btn btn-outline-primary" href="">Detail</a>
                                                        <a class="btn btn-outline-danger" href="">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Recent Sales End -->


                        <!-- Widgets Start -->
                        <div class="container-fluid pt-4 px-0">
                            <div class="row g-4">
                                <div class="col-sm-12 col-md-6 col-xl-4">
                                    <div class="h-100 bg-light rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="mb-0">Messages</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <div class="d-flex align-items-center border-bottom py-3">
                                            <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-0">Jhon Doe</h6>
                                                    <small>15 minutes ago</small>
                                                </div>
                                                <span>Short message goes here...</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center border-bottom py-3">
                                            <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-0">Jhon Doe</h6>
                                                    <small>15 minutes ago</small>
                                                </div>
                                                <span>Short message goes here...</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center border-bottom py-3">
                                            <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-0">Jhon Doe</h6>
                                                    <small>15 minutes ago</small>
                                                </div>
                                                <span>Short message goes here...</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center pt-3">
                                            <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-0">Jhon Doe</h6>
                                                    <small>15 minutes ago</small>
                                                </div>
                                                <span>Short message goes here...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-xl-4">
                                    <div class="h-100 bg-light rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0">Calender</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <div id="calender"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-xl-4">
                                    <div class="h-100 bg-light rounded p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h6 class="mb-0">To Do List</h6>
                                            <a href="">Show All</a>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <input class="form-control bg-transparent" type="text" placeholder="Enter task">
                                            <button type="button" class="btn btn-primary ms-2">Add</button>
                                        </div>
                                        <div class="d-flex align-items-center border-bottom py-2">
                                            <input class="form-check-input m-0" type="checkbox">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 align-items-center justify-content-between">
                                                    <span>Short task goes here...</span>
                                                    <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center border-bottom py-2">
                                            <input class="form-check-input m-0" type="checkbox">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 align-items-center justify-content-between">
                                                    <span>Short task goes here...</span>
                                                    <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center border-bottom py-2">
                                            <input class="form-check-input m-0" type="checkbox" checked>
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 align-items-center justify-content-between">
                                                    <span><del>Short task goes here...</del></span>
                                                    <button class="btn btn-sm text-primary"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center border-bottom py-2">
                                            <input class="form-check-input m-0" type="checkbox">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 align-items-center justify-content-between">
                                                    <span>Short task goes here...</span>
                                                    <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center pt-2">
                                            <input class="form-check-input m-0" type="checkbox">
                                            <div class="w-100 ms-3">
                                                <div class="d-flex w-100 align-items-center justify-content-between">
                                                    <span>Short task goes here...</span>
                                                    <button class="btn btn-sm"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Widgets End -->
                    </div>
                    <div class="tab-pane fade" id="sale" role="tabpanel" aria-labelledby="sale-tab">
                        <div class="container-fluid pt-0 px-0">
                            <div class="row g-4">
                                <div class="col-sm-6 col-xl-4">
                                    <div class="bg-sale-1 rounded-4 p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="bg-light rounded-4 px-3 py-0">
                                                <i class="bi bi-bar-chart-line-fill fs-2 icon-color-1"></i>
                                            </span>
                                            <div class="ms-3">
                                                <p class="bg-sale-percent-1 rounded-4 px-2 text-dark">+280 %</p>
                                            </div>
                                        </div>
                                        <div class="text-light fs-6">Total Sales</div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-light fs-3 fw-bold">$169,527.89</div>
                                            <span class="text-light fs-6">This Month</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-light fs-3 fw-bold">$169,527.89</div>
                                            <span class="text-light fs-6">Today</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <div class="bg-sale-2 rounded-4 p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="bg-light rounded-4 px-3 py-0">
                                                <i class="bi bi-journal-check fs-2 icon-color-2"></i>
                                            </span>
                                            <div class="ms-3">
                                                <p class="bg-sale-percent-2 rounded-4 px-2">+280 %</p>
                                            </div>
                                        </div>
                                        <div class="text-light fs-6">Total Orders</div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-light fs-3 fw-bold">527.00</div>
                                            <span class="text-light fs-6">This Month</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-light fs-3 fw-bold">527.00</div>
                                            <span class="text-light fs-6">Today</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <div class="bg-sale-3 rounded-4 p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="bg-light rounded-4 px-3 py-0">
                                                <i class="bi bi-tags-fill fs-2 icon-color-3"></i>
                                            </span>
                                            <div class="ms-3">
                                                <p class="bg-sale-percent-3 rounded-4 px-2 text-light">-20 %</p>
                                            </div>
                                        </div>
                                        <div class="text-light fs-6">Total Products Sold</div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-light fs-3 fw-bold">527.00</div>
                                            <span class="text-light fs-6">This Month</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-light fs-3 fw-bold">527.00</div>
                                            <span class="text-light fs-6">Today</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    <?php include "include/footer.php"?>
</div>
    <!-- Content End -->
    <!-- Back to Top -->
<div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
</div>

   