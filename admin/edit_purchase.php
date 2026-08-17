<?php
    require_once 'include/dbconnection.php';

    // ------------------------------------------------------------------
    // Update Purchase (this is the EDIT page, so we UPDATE, not INSERT)
    // ------------------------------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addpurchase'])) {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['message'] = 'Invalid purchase record.';
            $_SESSION['message_type'] = 'danger';
            header('Location: purchase.php');
            exit();
        }

        $create_date      = $_POST['create_date'];
        $reference        = $_POST['reference'];
        $rate             = (float)($_POST['exchange_rate'] ?? 0);
        $tax              = (float)($_POST['tax'] ?? 0);
        $discount         = (float)($_POST['discount'] ?? 0);
        $shipping         = (float)($_POST['shipping'] ?? 0);
        $note             = $_POST['note'];
        $status           = $_POST['status'] ?? 'pending';
        $other_reference  = $_POST['other_reference'] ?? '';

        // company id and name
        $company_id = (int)$_POST['company'];
        $stmtCompany = $conn->prepare("SELECT name FROM company WHERE id = ?");
        $stmtCompany->bind_param("i", $company_id);
        $stmtCompany->execute();
        $stmtCompany->bind_result($company);
        $stmtCompany->fetch();
        $stmtCompany->close();

        // warehouse id and name
        $warehouse_id = (int)$_POST['warehouse'];
        $stmtWarehouse = $conn->prepare("SELECT name FROM warehouse WHERE id = ?");
        $stmtWarehouse->bind_param("i", $warehouse_id);
        $stmtWarehouse->execute();
        $stmtWarehouse->bind_result($warehouse);
        $stmtWarehouse->fetch();
        $stmtWarehouse->close();

        // supplier id and name
        $supplier_id = (int)$_POST['supplier'];
        $stmtSupplier = $conn->prepare("SELECT name FROM supplier WHERE id = ?");
        $stmtSupplier->bind_param("i", $supplier_id);
        $stmtSupplier->execute();
        $stmtSupplier->bind_result($supplier_name);
        $stmtSupplier->fetch();
        $stmtSupplier->close();

        // product arrays from the dynamic table
        $product_ids   = $_POST['product_id'] ?? [];
        $product_codes = $_POST['product_code'] ?? [];
        $product_names = $_POST['product_name'] ?? [];
        $units         = $_POST['unit'] ?? [];
        $costs         = $_POST['cost'] ?? [];
        $qtys          = $_POST['qty'] ?? [];

        if (count($product_ids) === 0) {
            $_SESSION['message'] = 'Please add at least one product.';
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_purchase.php?id=' . $id);
            exit();
        }

        // calculate grand total
        $grand_total = 0.0;
        for ($i = 0; $i < count($product_ids); $i++) {
            $c = (float)($costs[$i] ?? 0);
            $q = (int)($qtys[$i] ?? 0);
            $grand_total += $c * $q;
        }

        $conn->begin_transaction();

        // ---- Update the purchase header (was previously INSERTing a
        //      brand-new row here, which is why edits weren't saving) ----
        $stmt = $conn->prepare("UPDATE purchases SET
                                    create_date = ?,
                                    reference = ?,
                                    supplier_id = ?,
                                    supplier_name = ?,
                                    company_id = ?,
                                    company = ?,
                                    warehouse_id = ?,
                                    warehouse = ?,
                                    rate = ?,
                                    tax = ?,
                                    discount = ?,
                                    shipping = ?,
                                    note = ?,
                                    status = ?,
                                    other_reference = ?,
                                    grand_total = ?
                                WHERE id = ?");
        if (!$stmt) {
            $conn->rollback();
            $_SESSION['message'] = 'Prepare failed (purchases): ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_purchase.php?id=' . $id);
            exit();
        }

        $stmt->bind_param(
            "ssisisisddddsssdi",
            $create_date,
            $reference,
            $supplier_id,
            $supplier_name,
            $company_id,
            $company,
            $warehouse_id,
            $warehouse,
            $rate,
            $tax,
            $discount,
            $shipping,
            $note,
            $status,
            $other_reference,
            $grand_total,
            $id
        );
        if (!$stmt->execute()) {
            $stmt->close();
            $conn->rollback();
            $_SESSION['message'] = 'Update failed (purchases): ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_purchase.php?id=' . $id);
            exit();
        }
        $stmt->close();

        // Get old purchase items before deleting them
        $oldItemsStmt = $conn->prepare("SELECT product_id, quantity FROM purchase_items WHERE purchase_id = ?");
        if (!$oldItemsStmt) {
            $conn->rollback();
            $_SESSION['message'] = 'Prepare failed (old items): ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_purchase.php?id=' . $id);
            exit();
        }
        $oldItemsStmt->bind_param("i", $id);
        $oldItemsStmt->execute();
        $oldItemsResult = $oldItemsStmt->get_result();
        $oldItems = $oldItemsResult->fetch_all(MYSQLI_ASSOC);
        $oldItemsStmt->close();

        // Reverse old stock
        $reverseStockStmt = $conn->prepare("UPDATE products SET quantity = IFNULL(quantity, 0) - ? WHERE id = ?");
        if (!$reverseStockStmt) {
            $conn->rollback();
            $_SESSION['message'] = 'Prepare failed (reverse stock): ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_purchase.php?id=' . $id);
            exit();
        }
        foreach ($oldItems as $oldItem) {
            $oldPid = (int)$oldItem['product_id'];
            $oldQty = (int)$oldItem['quantity'];
            $reverseStockStmt->bind_param("ii", $oldQty, $oldPid);
            if (!$reverseStockStmt->execute()) {
                $reverseStockStmt->close();
                $conn->rollback();
                $_SESSION['message'] = 'Failed to reverse old stock: ' . $reverseStockStmt->error;
                $_SESSION['message_type'] = 'danger';
                header('Location: edit_purchase.php?id=' . $id);
                exit();
            }
        }
        $reverseStockStmt->close();

        //---- Replace line items: clear old ones, insert current set ----//
        $delStmt = $conn->prepare("DELETE FROM purchase_items WHERE purchase_id = ?");
        $delStmt->bind_param("i", $id);
        if (!$delStmt->execute()) {
            $delStmt->close();
            $conn->rollback();
            $_SESSION['message'] = 'Failed to clear old items: ' . $delStmt->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_purchase.php?id=' . $id);
            exit();
        }
        $delStmt->close();

        // prepare quantity
        $stmtQuantity = $conn->prepare("UPDATE products SET quantity = IFNULL(quantity, 0) + ? WHERE id = ?");

        $itemStmt = $conn->prepare("INSERT INTO purchase_items 
                                    (
                                        purchase_id, 
                                        product_id, 
                                        product_code, 
                                        product_name, 
                                        unit, 
                                        cost, 
                                        quantity
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$itemStmt) {
            $conn->rollback();
            $_SESSION['message'] = 'Prepare failed (items): ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: edit_purchase.php?id=' . $id);
            exit();
        }

        for ($i = 0; $i < count($product_ids); $i++) {
            $pid   = (int)$product_ids[$i];
            $pcode = $product_codes[$i] ?? '';
            $pname = $product_names[$i] ?? '';
            $unit  = $units[$i] ?? '';
            $cost  = (float)($costs[$i] ?? 0);
            $qty   = (int)($qtys[$i] ?? 0);

            $itemStmt->bind_param("iisssdi", $id, $pid, $pcode, $pname, $unit, $cost, $qty);
            if (!$itemStmt->execute()) {
                $itemStmt->close();
                $conn->rollback();
                $_SESSION['message'] = 'Insert failed (items): ' . $itemStmt->error;
                $_SESSION['message_type'] = 'danger';
                header('Location: edit_purchase.php?id=' . $id);
                exit();
            }
            
            $stmtQuantity->bind_param("ii", $qty, $pid);
            if (!$stmtQuantity->execute()) {
                $stmtQuantity->close();
                $itemStmt->close();
                $conn->rollback();

                $_SESSION['message'] = 'Stock update failed: ' . $stmtQuantity->error;
                $_SESSION['message_type'] = 'danger';

                header('Location: add_purchase.php');
                exit();
            }

            // $stmtQuantity->close();
        }
        $itemStmt->close();

        $conn->commit();

        $_SESSION['message'] = 'Purchase updated successfully!';
        $_SESSION['message_type'] = 'success';
        header('Location: purchase.php');
        exit();
    }

    // ------------------------------------------------------------------
    // Load the purchase for editing
    // ------------------------------------------------------------------
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        header('Location: purchase.php');
        exit();
    }

    $edit_sql = "SELECT 
                    id,
                    create_date,
                    reference,
                    other_reference,
                    company_id,
                    company,
                    warehouse_id,
                    warehouse,
                    supplier_id,
                    supplier_name,
                    grand_total,
                    rate,
                    tax,
                    discount,
                    shipping,
                    paid,
                    (grand_total - IFNULL(paid, 0)) AS balance,
                    status,
                    payment_status,
                    note
                FROM purchases 
                WHERE id = ?
            ";
    $stmtEdit = $conn->prepare($edit_sql);
    $stmtEdit->bind_param("i", $id);
    $stmtEdit->execute();
    $edit_purchase = $stmtEdit->get_result()->fetch_assoc();
    $stmtEdit->close();

    if (!$edit_purchase) {
        header('Location: purchase.php');
        exit();
    }

    // Existing line items, so the product table is pre-populated on load
    $itemsStmt = $conn->prepare("SELECT product_id, product_code, product_name, unit, cost, quantity 
                                  FROM purchase_items WHERE purchase_id = ?");
    $itemsStmt->bind_param("i", $id);
    $itemsStmt->execute();
    $purchase_items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $itemsStmt->close();

    $product = $conn->query('SELECT id, code, name, unit, cost FROM products ORDER BY name ASC');
    $company = $conn->query('SELECT id, name FROM company');
    $suppliers = $conn->query('SELECT id, name FROM supplier');
    $warehouse = $conn->query('SELECT id, name FROM warehouse');

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
        <div class="content">
            <?php include "include/navbar.php"?>
            <div class="container-fluid pt-3 px-3">
                <div class="bg-light rounded p-4">
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <form action="edit_purchase.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= (int)$edit_purchase['id'] ?>">
                        <div class="row g-3">
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" id="date" name="create_date" value="<?= $edit_purchase['create_date'] ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="reference_no">Reference</label>
                                    <input type="text" class="form-control" id="reference_no" name="reference" value="<?= $edit_purchase['reference'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="other_reference">Other Reference</label>
                                    <input type="text" class="form-control" id="other_reference" name="other_reference" value="<?= $edit_purchase['other_reference'] ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label>
                                    <select id="supplierSelect" class="form-select" name="supplier">
                                        <?php
                                            while($sup = $suppliers->fetch_assoc()) {
                                                $selected = ($sup['id'] == $edit_purchase['supplier_id']) ? 'selected' : '';
                                                echo "<option value='" . $sup['id'] . "' $selected>" . htmlspecialchars($sup['name']) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="company">Company</label>
                                    <select id="companySelect" class="form-select" name="company">
                                        <?php
                                            while($com = $company->fetch_assoc()) {
                                                $selected = ($com['id'] == $edit_purchase['company_id']) ? 'selected' : '';
                                                echo "<option value='" . $com['id'] . "' $selected>" . htmlspecialchars($com['name']) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="warehouse">Warehouse</label>
                                    <select id="warehouseSelect" class="form-select" name="warehouse">
                                        <?php
                                            while($war = $warehouse->fetch_assoc()) {
                                                $selected = ($war['id'] == $edit_purchase['warehouse_id']) ? 'selected' : '';
                                                echo "<option value='" . $war['id'] . "' $selected>" . htmlspecialchars($war['name']) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="exchange_rate">Exchange Rate</label>
                                    <input type="text" class="form-control" id="exchange_rate" name="exchange_rate" value="<?= $edit_purchase['rate'] ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Please Select Product...</label>
                                    <select id="productSelect" class="form-select" name="product">
                                        <option value="">-- Choose a Product --</option>
                                        <?php
                                            if ($product && $product->num_rows > 0) {
                                                while($pro = $product->fetch_assoc()) {
                                                    echo "<option value='" . (int)$pro['id'] . "'"
                                                        . " data-code='" . htmlspecialchars($pro['code'], ENT_QUOTES) . "'"
                                                        . " data-name='" . htmlspecialchars($pro['name'], ENT_QUOTES) . "'"
                                                        . " data-unit='" . htmlspecialchars($pro['unit'], ENT_QUOTES) . "'"
                                                        . " data-cost='" . htmlspecialchars($pro['cost'], ENT_QUOTES) . "'>"
                                                        . htmlspecialchars($pro['code'] . ' - ' . $pro['name'])
                                                        . "</option>";
                                                }
                                            } else {
                                                echo '<option value="">No Data Display</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead>
                                            <tr class="bg-secondary text-light text-center">
                                                <th>No</th>
                                                <th>Product Name</th>
                                                <th width="10%">Unit</th>
                                                <th width="10%">Cost</th>
                                                <th width="10%">Quantity</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-title">
                                            <?php
                                                // Pre-populate rows from the existing purchase_items,
                                                // using the SAME markup shape the JS "add product"
                                                // handler builds, so recalcTotals(), the qty/cost
                                                // inputs, remove-row, and "select again -> bump qty"
                                                // all keep working without any extra JS.
                                                $rowNo = 1;
                                                foreach ($purchase_items as $item):
                                                    $pid   = (int)$item['product_id'];
                                                    $pcode = htmlspecialchars($item['product_code'], ENT_QUOTES);
                                                    $pname = htmlspecialchars($item['product_name'], ENT_QUOTES);
                                                    $unit  = htmlspecialchars($item['unit'], ENT_QUOTES);
                                                    $cost  = (float)$item['cost'];
                                                    $qty   = (int)$item['quantity'];
                                            ?>
                                            <tr id="row-<?= $pid ?>">
                                                <td class="text-center row-no"><?= $rowNo++ ?></td>
                                                <td>
                                                    <?= $pcode ?> - <?= $pname ?>
                                                    <input type="hidden" name="product_id[]" value="<?= $pid ?>">
                                                    <input type="hidden" name="product_code[]" value="<?= $pcode ?>">
                                                    <input type="hidden" name="product_name[]" value="<?= $pname ?>">
                                                    <span class="fw-bold float-end px-2"><i class="bi bi-pencil-square cursor-pointer" data-bs-toggle="modal" data-bs-target="#editproduct" title="Edit"></i></span>
                                                    <span class="fw-bold float-end"><i class="bi bi-chat-dots cursor-pointer" data-bs-toggle="modal" data-bs-target="#comment" title="Comment"></i></span>
                                                </td>
                                                <td class="text-center">
                                                    <?= $unit ?>
                                                    <input type="hidden" name="unit[]" value="<?= $unit ?>">
                                                </td>
                                                <td class="text-center">
                                                    <input type="text" class="form-control text-center cost-input" name="cost[]" value="<?= number_format($cost, 2, '.', '') ?>" step="0.01">
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" class="form-control text-center qty-input" name="qty[]" value="<?= $qty ?>" min="1" data-id="<?= $pid ?>">
                                                </td>
                                                <td class="text-center"><a class="remove-row"><i class="bi bi-trash text-danger cursor-pointer fs-4"></i></a></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light">
                                                <td colspan="3" class="text-end fw-bold">Totals</td>
                                                <td class="text-center"><strong>$ <span id="total-cost">0.00</span></strong></td>
                                                <td class="text-center"><strong><span id="total-qty">0</span></strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="tax">Tax</label>
                                    <input type="text" class="form-control" id="tax" name="tax" value="<?= $edit_purchase['tax'] ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="text" class="form-control" id="discount" name="discount" value="<?= $edit_purchase['discount'] ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="Shipping">Shipping</label>
                                    <input type="text" class="form-control" id="Shipping" name="shipping" value="<?= $edit_purchase['shipping'] ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-select" id="status" name="status" disabled>
                                        <option value="pending" <?= ($edit_purchase['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="approved" <?= ($edit_purchase['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                                        <option value="rejected" <?= ($edit_purchase['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="floatingTextarea2">Noted</label>
                                <div class="form-floating">
                                    <textarea class="form-control" name="note" id="floatingTextarea2" style="height: 100px"><?= htmlspecialchars($edit_purchase['note'] ?? '') ?></textarea>
                                    <label for="floatingTextarea2">Write something here...!</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <input type="submit" name="addpurchase" value="Submit" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include "include/footer.php"?>
		</div>
		<?php include "include/foot.php"?>
	</div>
    <script>
        $(document).ready(function() {
            $(document).ready(function () {
                $('#productSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Choose a products --',
                    allowClear: true,
                    width: '100%'
                });

                $('#supplierSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Choose a supplier --',
                    allowClear: true,
                    width: '100%'
                });

                $('#warehouseSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Choose a warehouse --',
                    allowClear: true,
                    width: '100%'
                });

                $('#companySelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Choose a company --',
                    allowClear: true,
                    width: '100%'
                });
            });

            // Recalculate totals
            function recalcTotals() {
                var totalQty = 0;
                var totalcost = 0;
                $('tbody.text-title tr').each(function() {
                    var qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    var cost = parseFloat($(this).find('.cost-input').val()) || 0;
                    totalQty += qty;
                    totalcost += cost * qty;
                });
                $('#total-qty').text(totalQty);
                $('#total-cost').text(totalcost.toFixed(2));
            }

            // When a product is selected, add it to the table (or increase qty if exists)
            $('#productSelect').on('change', function() {
                var $opt = $(this).find('option:selected');
                var id = $opt.val();
                if (!id) return;

                // if row exists (including rows pre-populated from the DB), increase qty
                var $existing = $('#row-' + id);
                if ($existing.length) {
                    var $qty = $existing.find('.qty-input');
                    $qty.val(parseInt($qty.val() || 0) + 1).trigger('change');
                    $(this).val(null).trigger('change');
                    return;
                }

                var code = $opt.data('code') || '';
                var name = $opt.data('name') || '';
                var unit = $opt.data('unit') || '';
                var cost = parseFloat($opt.data('cost')) || 0;

                var idx = $('tbody.text-title tr').length + 1;
                var row = '<tr id="row-' + id + '">' +
                            '<td class="text-center row-no">' + idx + '</td>' +
                            '<td>' + $('<div>').text(code + ' - ' + name).html() + 
                                '<input type="hidden" name="product_id[]" value="' + id + '">' +
                                '<input type="hidden" name="product_code[]" value="' + $('<div>').text(code).html() + '">' +
                                '<input type="hidden" name="product_name[]" value="' + $('<div>').text(name).html() + '">' +
                                '<span class="fw-bold float-end px-2"><i class="bi bi-pencil-square cursor-pointer" data-bs-toggle="modal" data-bs-target="#editproduct" title="Edit"></i></span>' +
                                '<span class="fw-bold float-end"><i class="bi bi-chat-dots cursor-pointer" data-bs-toggle="modal" data-bs-target="#comment" title="Comment"></i></span>' +
                            '</td>' +
                            '<td class="text-center">' + $('<div>').text(unit).html() + '<input type="hidden" name="unit[]" value="' + $('<div>').text(unit).html() + '"></td>' +
                            '<td class="text-center"><input type="text" class="form-control text-center cost-input" name="cost[]" value="' + cost.toFixed(2) + '" step="0.01"></td>' +
                            '<td class="text-center"><input type="number" class="form-control text-center qty-input" name="qty[]" value="1" min="1" data-id="' + id + '"></td>' +
                            '<td class="text-center"><a class="remove-row"><i class="bi bi-trash text-danger cursor-pointer fs-4"></i></a></td>' +
                        '</tr>';
                $('tbody.text-title').append(row);
                recalcTotals();
                $(this).val(null).trigger('change');
            });

            // remove row
            $('body').on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateRowNumbers();
                recalcTotals();
            });

            // qty change handler
            $('body').on('change', '.qty-input', function() {
                var v = parseInt($(this).val() || 0);
                if (v < 1) $(this).val(1);
                recalcTotals();
            });
            recalcTotals(); // runs on load too, so pre-populated rows show correct totals immediately

            function updateRowNumbers() {
                $('tbody.text-title tr').each(function(i){
                    $(this).find('.row-no').text(i + 1);
                });
            }

            $('#unitPrice').on('input', function() {
                $('#unitPriceDisplay').val($(this).val());
            });
            $('#quantity').on('input', function() {
                $('#quantityDisplay').val($(this).val());
            });

            $('#subtotalDisplay').val($('#unitPriceDisplay').val() * $('#quantityDisplay').val());

        });
    </script>

    <!-- Edit Product by Items -->
    <div class="modal fade" id="editproduct" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="update_id" id="update_id">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text w-75">Discount by Amount</span>
                                    <input type="number" class="form-control" aria-label="Sizing example input" step="1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text w-75">Discount by Percentage</span>
                                    <input type="text" class="form-control" aria-label="Sizing example input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text w-75">Unit Price</span>
                                    <input type="text" class="form-control" id="unitPrice" aria-label="Sizing example input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text w-75">Quantity</span>
                                    <input type="text" class="form-control" id="quantity" aria-label="Sizing example input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th width="25%">Unit Price</th>
                                                <th width="25%"><span id="unitPriceDisplay">0</span></th>
                                                <th width="25%">Quantity</th>
                                                <th width="25%"><span id="quantityDisplay">0</span></th>
                                            </tr>
                                            <tr>
                                                <th width="50%" colspan="2">Subtotal</th>
                                                <th width="50%" colspan="2"><span id="subtotalDisplay">0</span></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateSupplier" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>