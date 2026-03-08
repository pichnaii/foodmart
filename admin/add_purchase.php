<?php
    require_once 'include/dbconnection.php';
    // Add Purchase
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addpurchase'])) {
        $create_date = $_POST['create_date'];
        $reference = $_POST['reference'];
        $company = $_POST['company'];
        $warehouse = $_POST['warehouse'];
        $rate = (float)($_POST['exchange_rate'] ?? 0);
        $tax = (float)($_POST['tax'] ?? 0);
        $discount = (float)($_POST['discount'] ?? 0);
        $shipping = (float)($_POST['shipping'] ?? 0);
        $note = $_POST['note'];

        // supplier id and name
        $supplier_id = (int)$_POST['supplier'];
        $stmtSupplier = $conn->prepare("SELECT name FROM supplier WHERE id = ?");
        $stmtSupplier->bind_param("i", $supplier_id);
        $stmtSupplier->execute();
        $stmtSupplier->bind_result($supplier_name);
        $stmtSupplier->fetch();
        $stmtSupplier->close();

        // product arrays from the dynamic table
        $product_ids = $_POST['product_id'] ?? [];
        $product_codes = $_POST['product_code'] ?? [];
        $product_names = $_POST['product_name'] ?? [];
        $units = $_POST['unit'] ?? [];
        $costs = $_POST['cost'] ?? [];
        $qtys = $_POST['qty'] ?? [];

        // basic validation: must have at least one product row
        if (count($product_ids) === 0) {
            $_SESSION['message'] = 'Please add at least one product.';
            $_SESSION['message_type'] = 'danger';
            header('Location: add_purchase.php');
            exit();
        }

        // calculate grand total
        $grand_total = 0.0;
        for ($i = 0; $i < count($product_ids); $i++) {
            $c = (float)($costs[$i] ?? 0);
            $q = (int)($qtys[$i] ?? 0);
            $grand_total += $c * $q;
        }

        // Use transaction: insert into purchases then purchase_items
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO purchases (create_date, reference, supplier_id, supplier_name, company, warehouse, rate, tax, discount, shipping, note, grand_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $conn->rollback();
            $_SESSION['message'] = 'Prepare failed (purchases): ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: add_purchase.php');
            exit();
        }

        $stmt->bind_param("ssisssddddsd", $create_date, $reference, $supplier_id, $supplier_name, $company, $warehouse, $rate, $tax, $discount, $shipping, $note, $grand_total);
        if (!$stmt->execute()) {
            $stmt->close();
            $conn->rollback();
            $_SESSION['message'] = 'Insert failed (purchases)' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: add_purchase.php');
            exit();
        }
        $purchase_id = $conn->insert_id;
        $stmt->close();

        // Prepare purchase_items insert
        $itemStmt = $conn->prepare("INSERT INTO purchase_items (purchase_id, product_id, product_code, product_name, unit, cost, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$itemStmt) {
            $conn->rollback();
            $_SESSION['message'] = 'Prepare failed (items): ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: add_purchase.php');
            exit();
        }

        // Loop and insert each item
        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = (int)$product_ids[$i];
            $pcode = $product_codes[$i] ?? '';
            $pname = $product_names[$i] ?? '';
            $unit = $units[$i] ?? '';
            $cost = (float)($costs[$i] ?? 0);
            $qty = (int)($qtys[$i] ?? 0);

            $itemStmt->bind_param("iisssdi", $purchase_id, $pid, $pcode, $pname, $unit, $cost, $qty);
            if (!$itemStmt->execute()) {
                $itemStmt->close();
                $conn->rollback();
                $_SESSION['message'] = 'Insert failed (items): ' . $itemStmt->error;
                $_SESSION['message_type'] = 'danger';
                header('Location: add_purchase.php');
                exit();
            }
        }
        $itemStmt->close();

        $conn->commit();

        $_SESSION['message'] = 'Purchase added Successfully!';
        $_SESSION['message_type'] = 'success';
        header('Location: purchase.php');
        exit();
    }

    $sql = "SELECT * FROM categories ORDER BY created_date DESC";
    $result = $conn->query($sql);
    $product = $conn->query('SELECT id, code, name, unit, cost FROM products ORDER BY name ASC');
    $suppliers = $conn->query('SELECT id, name FROM supplier');

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
                    <form action="add_purchase.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" id="date" name="create_date" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="reference">Reference</label>
                                    <input type="text" class="form-control" id="reference" name="reference">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label>
                                    <select id="supplierSelect" class="form-select" name="supplier">
                                        <?php
                                            while($sup = $suppliers->fetch_assoc()) {
                                                echo "<option value='" . $sup['id'] . "'>" . htmlspecialchars($sup['name']) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="company">Company</label>
                                    <input type="text" class="form-control" id="company" name="company">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="warehouse">Warehouse</label>
                                    <input type="text" class="form-control" id="warehouse" name="warehouse">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="exchange_rate">Exchange Rate</label>
                                    <input type="text" class="form-control" id="exchange_rate" name="exchange_rate">
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
                                    <table class="table text-start align-middle table-bordered table-hover mb-0">
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
                                            <!-- Dynamic rows will be added here -->
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
                                    <input type="text" class="form-control" id="tax" name="tax">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="text" class="form-control" id="discount" name="discount">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="Shipping">Shipping</label>
                                    <input type="text" class="form-control" id="Shipping" name="shipping">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="floatingTextarea2">Noted</label>
                                <div class="form-floating">
                                    <textarea class="form-control" name="note" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
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
            // Initialize Select2
            $(document).ready(function () {
                $('#productSelect').select2({
                    theme: 'bootstrap-5',           // matches Bootstrap styling
                    placeholder: '-- Choose a category --',
                    allowClear: true,               // shows an X to clear selection
                    width: '100%'                   // full width of the container
                });

                $('#supplierSelect').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Choose a supplier --',
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

                // if row exists, increase qty
                var $existing = $('#row-' + id);
                if ($existing.length) {
                    var $qty = $existing.find('.qty-input');
                    $qty.val(parseInt($qty.val() || 0) + 1).trigger('change');
                    // reset select
                    $(this).val(null).trigger('change');
                    return;
                }

                var code = $opt.data('code') || '';
                var name = $opt.data('name') || '';
                var unit = $opt.data('unit') || '';
                var cost = parseFloat($opt.data('cost')) || 0;

                // compute row number
                var idx = $('tbody.text-title tr').length + 1;

                // var row = '<tr id="row-' + id + '">' +
                //             '<td class="text-center row-no">' + idx + '</td>' +
                //             '<td>' + $('<div>').text(code + ' - ' + name).html() + '</td>' +
                //             '<td class="text-center">' + $('<div>').text(unit).html() + '</td>' +
                //             '<td class="text-center"><input type="text" class="form-control text-center cost-input" name="cost[]" value="' + cost.toFixed(2) + '" step="0.01" disabled></td>' +
                //             '<td class="text-center"><input type="text" class="form-control text-center qty-input" name="qty[]" value="1" min="1" data-id="' + id + '"></td>' +
                //             '<td class="text-center"><a class="remove-row"><i class="bi bi-trash text-danger cursor-pointer fs-4"></i></a></td>' +
                //         '</tr>';
                var row = '<tr id="row-' + id + '">' +
                            '<td class="text-center row-no">' + idx + '</td>' +
                            '<td>' + $('<div>').text(code + ' - ' + name).html() + 
                                '<input type="hidden" name="product_id[]" value="' + id + '">' +
                                '<input type="hidden" name="product_code[]" value="' + $('<div>').text(code).html() + '">' +
                                '<input type="hidden" name="product_name[]" value="' + $('<div>').text(name).html() + '">' +
                            '</td>' +
                            '<td class="text-center">' + $('<div>').text(unit).html() + '<input type="hidden" name="unit[]" value="' + $('<div>').text(unit).html() + '"></td>' +
                            '<td class="text-center"><input type="text" class="form-control text-center cost-input" name="cost[]" value="' + cost.toFixed(2) + '" step="0.01"></td>' +
                            '<td class="text-center"><input type="number" class="form-control text-center qty-input" name="qty[]" value="1" min="1" data-id="' + id + '"></td>' +
                            '<td class="text-center"><a class="remove-row"><i class="bi bi-trash text-danger cursor-pointer fs-4"></i></a></td>' +
                        '</tr>';

                $('tbody.text-title').append(row);
                recalcTotals();
                // reset select
                $(this).val(null).trigger('change');
            });

            // remove row
            $('body').on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateRowNumbers();
                recalcTotals();
            });

            // qty change handler (you can expand to update totals)
            $('body').on('change', '.qty-input', function() {
                var v = parseInt($(this).val() || 0);
                if (v < 1) $(this).val(1);
                recalcTotals();
                // optional: recalc totals here
            });

            recalcTotals();

            function updateRowNumbers() {
                $('tbody.text-title tr').each(function(i){
                    $(this).find('.row-no').text(i+1);
                });
            }
        });
    </script>
</body>
</html>