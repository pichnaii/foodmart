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
        $where[]  = "DATE(create_date) >= ?";
        $params[] = $start_date;
        $types   .= 's';
    }
    if (!empty($end_date)) {
        $where[]  = "DATE(create_date) <= ?";
        $params[] = $end_date;
        $types   .= 's';
    }
    if (!empty($supplier)) {
        $where[]  = "supplier_name = ?";
        $params[] = $supplier;
        $types   .= 's';
    }
    if (!empty($warehouse)) {
        $where[]  = "warehouse = ?";
        $params[] = $warehouse;
        $types .= 's';
    }
    if (!empty($company)) {
        $where[]  = "company = ?";
        $params[] = $company;
        $types   .= 's';
    }
    if (!empty($reference)) {
        $where[]  = "reference = ?";
        $params[] = $reference;
        $types   .= 's';
    }

    $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

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
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ... $params);
    }
    $stmt->execute();
    $purchase_report = $stmt->get_result();

    // Store all purchases in array
    $purchases = [];
    $purchase_ids = [];
    while ($row = $purchase_report->fetch_assoc()) {
        $purchases[] = $row;
        $purchase_ids[] = (int)$row['id'];
    }
    $stmt->close();

    $items_by_purchase = [];
    if (!empty($purchase_ids)) {
        $placeholders = implode(',', array_fill(0, count($purchase_ids), '?'));
        $types_items  = str_repeat('i', count($purchase_ids));

        $sql_items = "SELECT purchase_id, product_code, product_name, quantity, cost, (quantity * IFNULL(cost, 0)) AS subtotal, item_discount, unit
                    FROM purchase_items 
                    WHERE purchase_id IN ($placeholders)";

        $stmt_items = $conn->prepare($sql_items);
        if ($stmt_items) {
            $stmt_items->bind_param($types_items, ...$purchase_ids);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();

            while ($item = $result_items->fetch_assoc()) {
                $items_by_purchase[$item['purchase_id']][] = $item;
            }
            $stmt_items->close();
        }
    }

    $suppliers = $conn->query('SELECT id, name FROM supplier ORDER BY name ASC');
    $companys = $conn->query('SELECT id, name FROM company ORDER BY name ASC');
    $warehouses = $conn->query('SELECT id, name FROM warehouse ORDER BY name ASC');
    $companies = $conn->query("SELECT id, note, name, local_name, local_address, phone FROM company LIMIT 1")->fetch_assoc();
    if (!$companies) {
        $companies = [
            'local_name' => 'Company Name',
            'name'       => 'Company Name'
        ];
    }
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
                                            <p class="p-0 m-0">របាយការណ៍បញ្ជាទិញលម្អិត</p>
                                            <p class="p-0 m-0">Purchase Details Reports</p>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered border-dark" style="font-size: 12px !important;">
                                    <thead class="text-center bg-success text-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Company</th>
                                            <th>Warehouse</th>
                                            <th>Supplier</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Cost</th>
                                            <th>Item Dis.</th>
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
                                        <?php if (count($purchases) > 0): ?>
                                            <?php
                                            $no = 1;
                                            $grand_tax      = 0;
                                            $grand_discount = 0;
                                            $grand_shipping = 0;
                                            $grand_subtotal = 0;
                                            $grand_total    = 0;
                                            $grand_qty      = 0;
                                            $grand_cost     = 0;
                                            $grand_item_dis = 0;
                                            $grand_paid     = 0;
                                            $grand_balance  = 0;
                                            ?>

                                            <?php foreach ($purchases as $pur): ?>
                                                <?php
                                                    // Totals
                                                    $grand_tax      += (float)$pur['tax'];
                                                    $grand_discount += (float)$pur['discount'];
                                                    $grand_shipping += (float)$pur['shipping'];
                                                    $grand_subtotal += (float)$pur['subtotal'];
                                                    $grand_total    += (float)$pur['grand_total'];
                                                    $grand_paid     += (float)$pur['paid'];
                                                    $grand_balance  += (float)$pur['balance'];

                                                    // Payment status
                                                    $payment_status_name  = 'Pending';
                                                    $payment_status_color = 'badge-orange';

                                                    if ($pur['paid'] >= $pur['grand_total'] && $pur['grand_total'] > 0) {
                                                        $payment_status_name  = 'Paid';
                                                        $payment_status_color = 'badge-green';
                                                    } elseif ($pur['paid'] > 0 && $pur['paid'] < $pur['grand_total']) {
                                                        $payment_status_name  = 'Partial';
                                                        $payment_status_color = 'badge-brown';
                                                    }

                                                    $items = $items_by_purchase[$pur['id']] ?? [];
                                                    $item_count = count($items);
                                                ?>

                                                <?php if ($item_count > 0): ?>
                                                    <?php $row_count = 0; ?>
                                                    <?php foreach ($items as $item): ?>
                                                        <?php 
                                                            $grand_qty += $item['quantity'];
                                                            $grand_cost += $item['cost'];
                                                            $grand_item_dis += $item['item_discount'];
                                                            $row_count++; 
                                                        ?>
                                                        <tr data-id="<?= $pur['id'] ?>" data-paid="<?= $pur['paid'] ?>">
                                                            <?php if ($row_count == 1): ?>
                                                                <td class="text-center" rowspan="<?= $item_count ?>"><?= $no ?></td>
                                                                <td class="text-center" rowspan="<?= $item_count ?>"><?= date('d/m/Y', strtotime($pur['create_date'])) ?></td>
                                                                <td class="text-center" rowspan="<?= $item_count ?>"><?= htmlspecialchars($pur['reference']) ?></td>
                                                                <td class="text-start" rowspan="<?= $item_count ?>"><?= htmlspecialchars($pur['company']) ?></td>
                                                                <td class="text-start" rowspan="<?= $item_count ?>"><?= htmlspecialchars($pur['warehouse']) ?></td>
                                                                <td class="text-start" rowspan="<?= $item_count ?>"><?= htmlspecialchars($pur['supplier_name']) ?></td>
                                                            <?php endif; ?>

                                                            <td class="text-center"><?= htmlspecialchars($item['product_code']) ?></td>
                                                            <td class="text-start"><?= htmlspecialchars($item['product_name']) ?></td>
                                                            <td class="text-center"><?= htmlspecialchars($item['quantity']) ?></td>
                                                            <td class="text-center"><?= htmlspecialchars($item['unit']) ?></td>
                                                            <td class="text-end"><?= formatcurrency_usd($item['cost']) ?></td>
                                                            <td class="text-end"><?= formatcurrency_usd($item['item_discount']) ?></td>
                                                            <td class="text-end"><?= formatcurrency_usd($item['subtotal']) ?></td>

                                                            <?php if ($row_count == 1): ?>
                                                                <td class="text-end" rowspan="<?= $item_count ?>"><?= formatcurrency_usd($pur['tax']) ?></td>
                                                                <td class="text-end" rowspan="<?= $item_count ?>"><?= formatcurrency_usd($pur['discount']) ?></td>
                                                                <td class="text-end" rowspan="<?= $item_count ?>"><?= formatcurrency_usd($pur['shipping']) ?></td>
                                                                <td class="text-end" rowspan="<?= $item_count ?>"><?= formatcurrency_usd($pur['grand_total']) ?></td>
                                                                <td class="text-end" rowspan="<?= $item_count ?>"><?= formatcurrency_usd($pur['paid']) ?></td>
                                                                <td class="text-end" rowspan="<?= $item_count ?>"><?= formatcurrency_usd($pur['balance']) ?></td>
                                                                <td class="text-center" rowspan="<?= $item_count ?>">
                                                                    <span class="<?= $payment_status_color ?>"><?= $payment_status_name ?></span>
                                                                </td>
                                                            <?php endif; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <!-- No items -->
                                                    <tr data-id="<?= $pur['id'] ?>" data-paid="<?= $pur['paid'] ?>" style="cursor:pointer;">
                                                        <td class="text-center"><?= $no ?></td>
                                                        <td class="text-center"><?= date('d/m/Y', strtotime($pur['create_date'])) ?></td>
                                                        <td class="text-center"><?= htmlspecialchars($pur['reference']) ?></td>
                                                        <td class="text-start"><?= htmlspecialchars($pur['company']) ?></td>
                                                        <td class="text-start"><?= htmlspecialchars($pur['warehouse']) ?></td>
                                                        <td class="text-start"><?= htmlspecialchars($pur['supplier_name']) ?></td>
                                                        <td class="text-center">-</td>
                                                        <td class="text-start">No items</td>
                                                        <td class="text-end"><?= formatcurrency_usd($pur['grand_total']) ?></td>
                                                        <td class="text-end"><?= formatcurrency_usd($pur['tax']) ?></td>
                                                        <td class="text-end"><?= formatcurrency_usd($pur['discount']) ?></td>
                                                        <td class="text-end"><?= formatcurrency_usd($pur['shipping']) ?></td>
                                                        <td class="text-end"><?= formatcurrency_usd($pur['grand_total']) ?></td>
                                                        <td class="text-end"><?= formatcurrency_usd($pur['paid']) ?></td>
                                                        <td class="text-end"><?= formatcurrency_usd($pur['balance']) ?></td>
                                                        <td class="text-center">
                                                            <span class="<?= $payment_status_color ?>"><?= $payment_status_name ?></span>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>

                                                <?php $no++; ?>
                                            <?php endforeach; ?>

                                            <?php 
                                                $paid_all_status = '';
                                                $paid_all_color  = '';
                                                if ($grand_total - $grand_paid == 0) {
                                                    $paid_all_status = 'Paid All';
                                                    $paid_all_color  = 'badge-paid-all';
                                                } else {
                                                    $paid_all_status = 'Not Paid All';
                                                    $paid_all_color  = 'badge-not-paid-all';
                                                }
                                            ?>

                                            <!-- Total Row -->
                                            <tr class="text-end fw-bold bg-light">
                                                <td colspan="8" class="text-end">Total:</td>
                                                <td class="text-center"><?= $grand_qty ?></td>
                                                <td class="text-center"></td>
                                                <td class="text-end"><?= formatcurrency_usd($grand_cost) ?></td>
                                                <td class="text-end"><?= formatcurrency_usd($grand_item_dis) ?></td>
                                                <td><?= formatcurrency_usd($grand_subtotal) ?></td>
                                                <td><?= formatcurrency_usd($grand_tax) ?></td>
                                                <td><?= formatcurrency_usd($grand_discount) ?></td>
                                                <td><?= formatcurrency_usd($grand_shipping) ?></td>
                                                <td><?= formatcurrency_usd($grand_total) ?></td>
                                                <td><?= formatcurrency_usd($grand_paid) ?></td>
                                                <td><?= formatcurrency_usd($grand_balance) ?></td>
                                                <td class="text-center"><span class="<?= $paid_all_color ?>"><?= $paid_all_status ?></span></td>
                                            </tr>

                                        <?php else: ?>
                                            <tr>
                                                <td colspan="20" class="text-center text-danger py-2 fs-5 fw-bold text-uppercase">No data found.</td>
                                            </tr>
                                        <?php endif; ?>
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