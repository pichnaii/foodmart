<?php
    $currencyCode = 'USD';
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
?>
<!-- Content Start -->
<div class="content">
    <?php include "include/navbar.php"?>
    <div class="container-fluid pt-4 px-4">
        <?php if(isset($_SESSION['message'])){ ?>
            <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" id="message" role="alert">
                Welcome, <?php echo htmlspecialchars($_SESSION['message']); ?>...!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php } ?>
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
                        <h6 class="mb-0 text-light float-end fs-4"><?= $formatter->formatCurrency($totalProduct['cost'], $currencyCode) ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="db_acc rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-chart-area fa-3x text-light"></i>
                    <div class="ms-3">
                        <p class="mb-2 text-light">Today Price Of Products</p>
                        <h6 class="mb-0 text-light float-end fs-4"><?= $formatter->formatCurrency($totalProduct['price'], $currencyCode) ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="db_box rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-chart-pie fa-3x text-light"></i>
                    <div class="ms-3">
                        <p class="mb-2 text-light">Total Revenue</p>
                        <h6 class="mb-0 text-light fs-4">$1234</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="db_naroto rounded">
                    <div class="d-flex align-items-center justify-content-between pt-3 px-3">
                        <i class="fa fa-money-bill-alt fa-3x text-light"></i>
                        <div class="ms-0">
                            <p class="mb-0 text-light">Today Purchase</p>
                            <h6 class="mb-0 text-light float-end fs-3"><?= $formatter->formatCurrency($purchasetoday['grand_total'], $currencyCode) ?></h6>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3">
                        <div class="ms-0">
                            <p class="mb-0 text-light">Last Month</p>
                            <h6 class="mb-0 text-light fs-5"><?= $formatter->formatCurrency($purchaseslastmonth['grand_total'], $currencyCode) ?></h6>
                        </div>
                        <div class="ms-0">
                            <p class="mb-0 text-light text-end">This Month</p>
                            <h6 class="mb-0 text-light float-end fs-5"><?= $formatter->formatCurrency($purchasesthismonth['grand_total'], $currencyCode) ?></h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="db_dashboard1 rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-chart-line fa-3x text-light"></i>
                    <div class="ms-3">
                        <p class="mb-2 text-light">Today Sale</p>
                        <h6 class="mb-0 text-light fs-4">$1234</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="db_dashboard2 rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-comments-dollar fa-3x text-light"></i>
                    <div class="ms-3">
                        <p class="mb-2 text-light">Today Sale</p>
                        <h6 class="mb-0 text-light fs-4">$1234</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="db_dashboard3 rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-hand-holding-usd fa-3x text-light"></i>
                    <div class="ms-3">
                        <p class="mb-2 text-light">Today Sale</p>
                        <h6 class="mb-0 text-light fs-4">$1234</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sale & Revenue End -->


    <!-- Sales Chart Start -->
    <div class="container-fluid pt-4 px-4">
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
    <div class="container-fluid pt-4 px-4">
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
    <div class="container-fluid pt-4 px-4">
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
    <?php include "include/footer.php"?>
</div>
    <!-- Content End -->
    <!-- Back to Top -->
<div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
</div>

   