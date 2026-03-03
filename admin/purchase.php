<?php
    require_once 'include/dbconnection.php';
    $purchase_data = ("SELECT 
                        id,
                        create_date,
                        reference,
                        company,
                        warehouse,
                        supplier,
                        grand_total,
                        paid,
                        (grand_total - IFNULL(paid, 0)) AS balance,
                        payment_status 
                        FROM purchases 
                        ORDER BY create_date DESC
                    ");
    $purchase = $conn->query($purchase_data);
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
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-0">
                        <a href="add_purchase.php" class="btn btn-primary mb-2">
                            <i class="fas fa-plus"></i> Add Purchase
                        </a>
                        <h5 class="mb-0 fw-bold text-title">Purchase List</h5>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div></div>
                        <div class="input-group w-25">
                            <input type="text" id="categorySearch" class="form-control w-50" placeholder="Search.....">
                        </div>
                    </div>
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="bg-secondary text-light text-center">
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Company</th>
                                    <th>Warehouse</th>
                                    <th>Supplier</th>
                                    <th>Grand Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php
                                    if ($purchase->num_rows > 0) {
                                        $no = 1;
                                        $g_total = 0;
                                        $total_paid = 0;
                                        $total_balance = 0;
                                        while($pur = $purchase->fetch_assoc()) {
                                            $g_total += $pur['grand_total'];
                                            $total_paid += $pur['paid'];
                                            $total_balance += $pur['balance'];

                                            $currencyCode = 'USD';
                                            $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
                                            $grandtotal = $formatter->formatCurrency($g_total, $currencyCode);
                                            $totalpaid = $formatter->formatCurrency($total_paid, $currencyCode);
                                            $totalbalance = $formatter->formatCurrency($total_balance, $currencyCode);

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
                                    <tr data-id="<?= $pur['id'] ?>" style="cursor:pointer;">
                                        <td class="text-center"><?= $no ?></td>
                                        <td class="text-center"><?= date('d/m/Y', strtotime($pur['create_date'])) ?></td>
                                        <td class="text-center"><?= $pur['reference'] ?></td>
                                        <td class="text-start"><?= $pur['company'] ?></td>
                                        <td class="text-start"><?= $pur['warehouse'] ?></td>
                                        <td class="text-start"><?= $pur['supplier'] ?></td>
                                        <td class="text-end"><?= $formatter->formatCurrency($pur['grand_total'], $currencyCode); ?></td>
                                        <td class="text-end"><?= $formatter->formatCurrency($pur['paid'], $currencyCode); ?></td>
                                        <td class="text-end"><?= $formatter->formatCurrency($pur['balance'], $currencyCode); ?></td>
                                        <td class="text-center"><span class="<?= $payment_status_color ?>"><?= $payment_status_name ?></span></td>
                                        <td class="text-center">
                                            <a class="edit-btn" 
                                                data-id="<?= $pur['id'] ?>" 
                                                data-code="<?= $pur['code'] ?>" 
                                                data-name="<?= $pur['name'] ?>" 
                                                data-created_date="<?= $pur['created_date'] ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#EditUser">
                                                <i class="bi bi-plus-circle-fill cursor-pointer fs-4"></i>
                                            </a>
                                            <a class="edit-btn" 
                                                data-id="<?= $pur['id'] ?>" 
                                                data-code="<?= $pur['code'] ?>" 
                                                data-name="<?= $pur['name'] ?>" 
                                                data-created_date="<?= $pur['created_date'] ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#EditUser">
                                                <i class="bi bi-pencil-square cursor-pointer fs-4"></i>
                                            </a>
                                            <a class="delete-btn" data-id="<?= $pur['id'] ?>" data-bs-toggle="modal" data-bs-target="#delete">
                                                <i class="bi bi-trash text-danger cursor-pointer fs-4"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    $no++;
                                    } 
                                } else { ?>
                                    <tr><td colspan='4' class='text-center text-danger'>No products found.</td></tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="6" class="text-end fw-bold">Totals</td>
                                    <td class="text-end"><strong><?= $grandtotal ?></strong></td>
                                    <td class="text-end"><strong><?= $totalpaid ?></strong></td>
                                    <td class="text-end"><strong><?= $totalbalance ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php include "include/footer.php"?>
		</div>
		<?php include "include/foot.php"?>
	</div>
    <!-- Purchase Details Modal -->
    <div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Purchase Details - <span id="modal_purchase_id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <strong>Date:</strong> <span id="modal_purchase_date"></span><br>
                        <strong>Reference:</strong> <span id="modal_purchase_ref"></span><br>
                        <strong>Supplier:</strong> <span id="modal_purchase_supplier"></span><br>
                        <strong>Company:</strong> <span id="modal_purchase_company"></span><br>
                    </div>
                    <div class="text-center fs-4 fw-bold">Purchase Invoice / វិក្កយបត្របញ្ជាទិញ</div>
                    <hr style="border: 3px solid #000000 !important;">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="modal_items_table">
                            <thead class="bg-secondary text-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Unit</th>
                                    <th>Cost</th>
                                    <th>Qty</th>
                                    <th width="15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="modal_items_body"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Grand Total</td>
                                    <td class="text-end fw-bold" id="modal_grand_total"></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Paid</td>
                                    <td class="text-end fw-bold" id="modal_grand_total"></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Balance</td>
                                    <td class="text-end fw-bold" id="modal_grand_total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="printPurchase" class="btn btn-success">Print</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var code = $(this).data('code');
                var name = $(this).data('name');
                var date = $(this).data('created_date');
                
                $('#update_id').val(id);
                $('#edit_code').val(code);
                $('#edit_name').val(name);
                $('#edit_created_date').val(date);
            });

            $('.delete-btn').click(function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });

            // Category Search
            $('#categorySearch').on('keyup', function() {
                var category = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(category) > -1)
                });
            });

            // Initialize Select2
            $(document).ready(function () {
                $('#categorySelect').select2({
                    theme: 'bootstrap-5',           // matches Bootstrap styling
                    placeholder: '-- Choose a category --',
                    allowClear: true,               // shows an X to clear selection
                    width: '100%'                   // full width of the container
                });
            });


            // Purchase Details Modal
            $('tbody.text-title').on('click', 'tr[data-id]', function(e) {
                // ignore clicks on action buttons/icons to allow delete/edit to work
                if ($(e.target).closest('a,button,i').length) return;

                var purchaseId = $(this).data('id');
                // fetch details
                $.post('purchase_details.php', { purchase_id: purchaseId }, function(resp) {
                    if (!resp.success) {
                        alert(resp.error || 'Cannot load purchase details');
                        return;
                    }
                    var p = resp.purchase;
                    var items = resp.items || [];

                    // populate header info
                    $('#modal_purchase_id').text(p.id);
                    $('#modal_purchase_ref').text(p.reference || '');
                    $('#modal_purchase_date').text(new Date(p.create_date).toLocaleDateString());
                    $('#modal_purchase_supplier').text(p.supplier || '');
                    $('#modal_purchase_company').text(p.company || '');

                    // populate items
                    var $body = $('#modal_items_body').empty();
                    var grand = 0;
                    items.forEach(function(it, idx) {
                        var cost = parseFloat(it.cost) || 0;
                        var qty = parseInt(it.quantity) || 0;
                        var total = cost * qty;
                        grand += total;
                        var row = '<tr>' +
                            '<td class="text-center">' + (idx + 1) + '</td>' +
                            '<td>' + $('<div>').text(it.product_code).html() + '</td>' +
                            '<td>' + $('<div>').text(it.product_name).html() + '</td>' +
                            '<td class="text-center">' + $('<div>').text(it.unit).html() + '</td>' +
                            '<td class="text-end">$ ' + cost.toFixed(2) + '</td>' +
                            '<td class="text-center">' + qty + '</td>' +
                            '<td class="text-end">$ ' + total.toFixed(2) + '</td>' +
                            '</tr>';
                        $body.append(row);
                    });
                    $('#modal_grand_total').text('$ ' + (parseFloat(p.grand_total) || grand).toFixed(2));

                    var purchaseModal = new bootstrap.Modal(document.getElementById('purchaseModal'));
                    purchaseModal.show();

                    // print handler
                    $('#printPurchase').off('click').on('click', function() {
                        var w = window.open('', '_blank');
                        var html = '<html><head><title>Purchase #' + p.id + '</title>';
                        html += '<style>@page { size: A5; margin: 5mm; } body{ font-family: Arial, sans-serif; font-size:12px; } table{width:100%;border-collapse:collapse;} th,td{border:1px solid #000;padding:6px;text-align:left;} th{background:#eee;}</style>';
                        html += '</head><body>';
                        html += '<h4>Purchase #' + p.id + ' - ' + (p.reference||'') + '</h4>';
                        html += '<div><strong>Date:</strong> ' + new Date(p.create_date).toLocaleDateString() + '<br>';
                        html += '<strong>Supplier:</strong> ' + (p.supplier||'') + '<br>';
                        html += '<strong>Company:</strong> ' + (p.company||'') + '</div>';
                        html += '<hr>';
                        html += document.getElementById('modal_items_table').outerHTML;
                        html += '<script>window.print();<\/script>';
                        html += '</body></html>';
                        w.document.write(html);
                        w.document.close();
                    });

                }, 'json').fail(function() {
                    alert('Request failed');
                });
            });
        });
    </script>
</body>
</html>