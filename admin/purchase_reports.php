<?php 
    require_once 'include/dbconnection.php';
    $isAdmin    = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';

    $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : date('Y-m-d');
    $end_date   = isset($_GET['end_date'])   ? trim($_GET['end_date'])   : date('Y-m-d');
    $supplier   = isset($_GET['supplier'])   ? trim($_GET['supplier'])   : '';
    $company    = isset($_GET['company'])    ? trim($_GET['company'])    : '';
    $warehouse  = isset($_GET['warehouse'])  ? trim($_GET['warehouse'])  : '';
    $reference  = isset($_GET['reference'])  ? trim($_GET['reference'])  : '';

    $where = [];
    $params = [];
    $types  = '';

    if (!empty($start_date)) {
        $where[] = "DATE(create_date) >= ?";
        $params[] = $start_date;
        $types   .= 's';
    }
    if (!empty($end_date)) {
        $where[] = "DATE(create_date) <= ?";
        $params[] = $end_date;
        $types   .= 's';
    }
    if (!empty($supplier)) {
        $where[]  = "supplier_name = ?";
        $params[] = $supplier;
        $types   .= 's';
    }
    if (!empty($warehouse)) {
        $where[] = "warehouse = ?";
        $params[] = $warehouse;
        $types .= 's';
    }
    if (!empty($company)) {
        $where[] = "company = ?";
        $params[] = $company;
        $types .= 's';
    }
    if (!empty($reference)) {
        $where[] = "reference = ?";
        $params[] = $reference;
        $types .= 's';
    }

    $where_sql = '';
    if (!empty($where)) {
        $where_sql = 'WHERE ' . implode(' AND ', $where);
    }

    $purchase_data = ("SELECT 
                        id,
                        create_date,
                        reference,
                        company,
                        warehouse,
                        supplier_name,
                        rate,
                        tax,
                        discount,
                        shipping,
                        grand_total AS subtotal,
                        (grand_total + IFNULL(shipping, 0)) AS grand_total,
                        paid,
                        (grand_total + IFNULL(shipping, 0) - IFNULL(paid, 0)) AS balance,
                        payment_status 
                        FROM purchases
                        $where_sql 
                        ORDER BY id DESC
                    ");
    $stmt = $conn->prepare($purchase_data);
    if (!empty($params)) {
        $stmt->bind_param($types, ... $params);
    }
    $stmt->execute();
    $purchase_report = $stmt->get_result();
    // $purchase_report = $conn->query($purchase_data);

    $suppliers = $conn->query('SELECT id, name FROM supplier ORDER BY name ASC');
    $companys = $conn->query('SELECT id, name FROM company ORDER BY name ASC');
    $warehouses = $conn->query('SELECT id, name FROM warehouse ORDER BY name ASC');
    $companies = $conn->query('SELECT id, note, name, local_name, local_address, phone FROM company')->fetch_assoc();
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
                    <div class="d-flex align-items-center justify-content-start gap-2 mb-4">
                        <button class="btn btn-success mb-0" id="print">
                            <i class="bi bi-printer-fill pe-2"></i>Print
                        </button>
                        <button class="btn btn-warning mb-0">
                            <i class="bi bi-file-earmark-spreadsheet-fill pe-2"></i>Export
                        </button>
                        <button type="button" class="btn btn-primary mb-0" id="showHideForm">
                            <i class="bi bi-eye-fill pe-2"></i>
                            Show / Hide
                        </button>
                    </div>
                    <div class="row g-3 mb-2" id="formSection" style="display: none;">
                        <form method="GET" class="row g-3 w-100">
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                <label for="reference">Reference</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="<?= htmlspecialchars($reference) ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="company">Company</label>
                                    <select id="companySelect" class="form-select" name="company">
                                        <option value="">-- All Company --</option>
                                        <?php while($com = $companys->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($com['name']) ?>"
                                                <?= ($company === $com['name']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($com['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="supplier">Warehouse</label>
                                    <select id="warehouseSelect" class="form-select" name="warehouse">
                                        <option value="">-- All Warehouse --</option>
                                        <?php while($war = $warehouses->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($war['name']) ?>"
                                                <?= ($warehouse === $war['name']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($war['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label>
                                    <select id="supplierSelect" class="form-select" name="supplier">
                                        <option value="">-- All Suppliers --</option>
                                        <?php while($sup = $suppliers->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($sup['name']) ?>"
                                                <?= ($supplier === $sup['name']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($sup['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input 
                                    type="date"
                                    class="form-control"
                                    id="start_date"
                                    name="start_date"
                                    value="<?= htmlspecialchars($start_date) ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input 
                                        type="date"
                                        class="form-control"
                                        id="end_date"
                                        name="end_date"
                                        value="<?= htmlspecialchars($end_date) ?>">
                                </div>
                            </div>
                            <div class="modal-footer justify-content-start">
                                <div class="d-flex align-items-start">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bi bi-funnel-fill pe-2"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-12 print-container">
                            <div class="d-flex align-items-center justify-content-between mb-4 table-header">
                                <div class="text-left w-50 ps-4"><img src="../admin/img/logo1.png" style="width: 20%;border-radius: 50%;"></div>
                                <div class="text-center" style="line-height: 25px;width: 100%;">
                                    <div class="fs-2 fw-bold" style="font-family: 'Khmer Moul', sans-serif;"><strong><?= $companies['local_name'] ?></strong></div>
                                    <div class="fs-4"><strong><?= $companies['name'] ?></strong></div>
                                </div>
                                <div class="text-right w-50">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <i class="bi bi-receipt-cutoff pe-2 fs-1"></i> 
                                        <h6 class="mb-0 fw-bold text-title fs-5">
                                            <p class="p-0 m-0">របាយការណ៍បញ្ជាទិញ</p>
                                            <p class="p-0 m-0">Purchase Reports</p>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered border-dark">
                                    <thead class="text-center bg-success text-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Company</th>
                                            <th>Warehouse</th>
                                            <th>Supplier</th>
                                            <th>Subtotal</th>
                                            <th>VAT</th>
                                            <th>Discount</th>
                                            <th>Shipping</th>
                                            <th>Grand Total</th>
                                            <th>Paid</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-title text-center text-dark">
                                        <?php
                                            if ($purchase_report->num_rows > 0) {
                                                $no = 1;
                                                $currencyUSD = '$';
                                                $currencyKHR = '៛';
                                                $grand_total = 0;
                                                $total_paid = 0;
                                                $total_balance = 0;
                                                $subtotal = 0;
                                                // $grand_subtotal = 0;
                                                $sup_grand_total = 0;
                                                $grand_tax = 0;
                                                $grand_discount = 0;
                                                $grand_shipping = 0;
                                                $grand_paid = 0;
                                                $grand_balance = 0;
                                                while($pur = $purchase_report->fetch_assoc()) {
                                                    $grand_total += $pur['grand_total'];
                                                    $subtotal += $pur['subtotal'];
                                                    $total_paid += $pur['paid'];
                                                    $total_balance += $pur['balance'];

                                                    $grand_subtotal = $subtotal;
                                                    $sup_grand_total = $grand_total;
                                                    $totalpaid = $total_paid;
                                                    $totalbalance = $total_balance;

                                                    $grand_tax += $pur['tax'];
                                                    $grand_discount += $pur['discount'];
                                                    $grand_shipping += $pur['shipping'];
                                                    $grand_paid += $pur['paid'];
                                                    $grand_balance += $pur['balance'];

                                                    // $subtotal = $pur['grand_total'];
                                                    // $grandtotal_khr = $g_total * $pur['rate'];
                                                    // $totalpaid_khr = $total_paid * $pur['rate'];
                                                    // $totalbalance_khr = $total_balance * $pur['rate'];

                                                    $payment_status_name = '' ;
                                                    $payment_status_color = '';
                                                    if ($pur['paid'] == 0) {
                                                        $payment_status_name = 'Pending';
                                                        $payment_status_color = 'badge-orange';
                                                    } elseif ($pur['paid'] >= $pur['grand_total']) {
                                                        $payment_status_name = 'Paid';
                                                        $payment_status_color = 'badge-green';
                                                    } elseif ($pur['paid'] > 0 && $pur['paid'] < $pur['grand_total']) {
                                                        $payment_status_name = 'Partial';
                                                        $payment_status_color = 'badge-brown';
                                                    }
                                        ?>
                                            <tr data-id="<?= $pur['id'] ?>" data-paid="<?= $pur['paid'] ?>" style="cursor:pointer;">
                                                <td class="text-center"><?= $no ?></td>
                                                <td class="text-center"><?= date('d/m/Y', strtotime($pur['create_date'])) ?></td>
                                                <td class="text-center"><?= $pur['reference'] ?></td>
                                                <td class="text-start"><?= $pur['company'] ?></td>
                                                <td class="text-start"><?= $pur['warehouse'] ?></td>
                                                <td class="text-start"><?= $pur['supplier_name'] ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($pur['subtotal']) ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($pur['tax']) ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($pur['discount']) ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($pur['shipping']) ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($pur['grand_total']) ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($pur['paid']) ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($pur['balance']) ?></td>
                                                <td class="text-center"><span class="<?= $payment_status_color ?>"><?= $payment_status_name ?></span></td>
                                            </tr>
                                        <?php 
                                            $no++;
                                            }
                                        ?>
                                        <?php 
                                            $paid_all_status = '';
                                            $paid_all_color  = '';
                                            if ($sup_grand_total - $totalpaid == 0) {
                                                $paid_all_status = 'Paid All';
                                                $paid_all_color  = 'badge-paid-all';
                                            } else {
                                                $paid_all_status = 'Not Paid All';
                                                $paid_all_color  = 'badge-not-paid-all';
                                            }
                                        ?>
                                            <tr class="text-end">
                                                <td colspan="6" class="text-end fw-bold">Total:</td>
                                                <td><?= formatcurrency_usd($grand_subtotal) ?></td>
                                                <td><?= formatcurrency_usd($grand_tax) ?></td>
                                                <td><?= formatcurrency_usd($grand_discount) ?></td>
                                                <td><?= formatcurrency_usd($grand_shipping) ?></td>
                                                <td><?= formatcurrency_usd($sup_grand_total) ?></td>
                                                <td><?= formatcurrency_usd($grand_paid) ?></td>
                                                <td><?= formatcurrency_usd($grand_balance) ?></td>
                                                <td class="text-center"><span class="<?= $paid_all_color ?>"><?= $paid_all_status ?></span></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr><td colspan='14' class='text-center text-danger'>No purchase found.</td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include "include/footer.php"?>
		</div>
		<?php include "include/foot.php"?>
	</div>

    <script>
        $(document).ready(function() {
            $(document).on('click', '#showHideForm', function () {
                // $('#formSection').slideToggle(300);
                $('#formSection').stop(true, true).slideToggle(400, 'swing');
            });

            $('#supplierSelect').select2({
                theme: 'bootstrap-5',
                placeholder: '-- All Supplier --',
                allowClear: true,
                width: '100%'
            });

            $('#warehouseSelect').select2({
                theme: 'bootstrap-5',
                placeholder: '-- All Warehouse --',
                allowClear: true,
                width: '100%'
            });

            $('#companySelect').select2({
                theme: 'bootstrap-5',
                placeholder: '-- All Company --',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>
</html>