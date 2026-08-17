<?php
    require_once 'include/dbconnection.php';
    $isAdmin    = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';

    // update status for Owner only
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $update_status_id = (int)$_POST['update_status_id'];
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE purchases SET status = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('si', $status, $update_status_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Purchase status updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating purchase status: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
        header("Location: purchase.php");
        exit();
    }

    // add payment
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
        $payment_id = (int)$_POST['payment_id'];
        $paid = (float)$_POST['payment_amount'];
        $payment_date = $_POST['payment_date'];
        $payment_method = $_POST['payment_method'];
        $payment_note = $_POST['payment_note'];

        $stmtPayment = $conn->prepare("UPDATE purchases SET paid = IFNULL(paid, 0) + ?, payment_date = ?, payment_method = ?, payment_note = ? WHERE id = ?");
        if (!$stmtPayment) {
            die("Prepare failed: " . $conn->error);
        }
        $stmtPayment->bind_param('dsssi', $paid, $payment_date, $payment_method, $payment_note, $payment_id);
        if ($stmtPayment->execute()) {
            $_SESSION['message'] = "Payment added successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding payment: " . $stmtPayment->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmtPayment->close();
        header("Location: purchase.php");
        exit();
    }

    // add payback
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payback'])) {
        $payback_id     = (int)$_POST['payback_id'];
        $paid           = (float)$_POST['payback_amount'];
        $payment_note   = $_POST['payment_note'];

        $stmtPayment = $conn->prepare("UPDATE purchases SET paid = IFNULL(paid, 0) - ?, payment_note = ? WHERE id = ?");
        if (!$stmtPayment) {
            die("Prepare failed: " . $conn->error);
        }
        $stmtPayment->bind_param('dsi', $paid, $payment_note, $payback_id);
        if ($stmtPayment->execute()) {
            $_SESSION['message'] = "Payment added successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding payment: " . $stmtPayment->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmtPayment->close();
        header("Location: purchase.php");
        exit();
    }

    // approve purchase
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_approve'])) {
        $update_id = (int)$_POST['update_id'];
        $stmt = $conn->prepare("UPDATE purchases SET status = 'approved' WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $update_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Purchase approved successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error approving purchase: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
        header("Location: purchase.php");
        exit();
    }

    // unapprove purchase
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_unapprove'])) {
        $unapprove_id = (int)$_POST['unapprove_id'];
        $stmt = $conn->prepare("UPDATE purchases SET status = 'pending' WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $unapprove_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Purchase unapproved successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error unapproving purchase: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
        header("Location: purchase.php");
        exit();
    }

    // reject purchase
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_reject'])) {
        $reject_id = (int)$_POST['reject_id'];
        $stmt = $conn->prepare("UPDATE purchases SET status = 'rejected' WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $reject_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Purchase rejected successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error rejecting purchase: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
        header("Location: purchase.php");
        exit();
    }

    // delete purchase
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_delete'])) {
        $delete_id = (int)$_POST['delete_id'];

        $conn->begin_transaction();
        $stmtItems = $conn->prepare("DELETE FROM purchase_items WHERE purchase_id = ?");
        if (!$stmtItems) {
            $conn->rollback();
            die("Prepare failed: " . $conn->error);
        }
        $stmtItems->bind_param('i', $delete_id);
        if (!$stmtItems->execute()) {
            $err = $stmtItems->error;
            $stmtItems->close();
            $conn->rollback();
            $_SESSION['message'] = "Error deleting purchase items: " . $err;
            $_SESSION['message_type'] = "danger";
            header("Location: purchase.php");
            exit();
        }
        $stmtItems->close();

        $stmt = $conn->prepare("DELETE FROM purchases WHERE id = ?");
        if (!$stmt) {
            $conn->rollback();
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('i', $delete_id);
        if ($stmt->execute()) {
            $conn->commit();
            $_SESSION['message'] = "Purchase deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $conn->rollback();
            $_SESSION['message'] = "Error deleting purchase: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
        header("Location: purchase.php");
        exit();
    }


    $purchase_data = ("SELECT 
                        id,
                        create_date,
                        reference,
                        company,
                        warehouse,
                        supplier_name,
                        rate,
                        paid,
                        shipping,
                        grand_total AS subtotal,
                        (grand_total + IFNULL(shipping, 0)) AS grand_total,
                        IFNULL(grand_total, 0) + IFNULL(shipping, 0) - IFNULL(paid, 0) AS balance,
                        status,
                        payment_status
                        FROM purchases 
                        ORDER BY id DESC
                    ");
    $purchase = $conn->query($purchase_data);

    $companies = $conn->query('SELECT id, note, name, local_name, local_address, phone FROM company')->fetch_assoc();
    // $currencies = $conn->query('SELECT id, exchange_rate FROM currency WHERE currency_code != "USD"')->fetch_assoc();
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
                        <div></div>
                        <h5 class="mb-0 fw-bold text-title">Purchase List</h5>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a href="add_purchase.php" class="btn btn-primary mb-2">
                            <i class="fas fa-plus"></i> Add Purchase
                        </a>
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
                    <div class="table-responsive1">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="bg-secondary text-light text-center">
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Company</th>
                                    <th>Warehouse</th>
                                    <th>Supplier</th>
                                    <th>Subtotal</th>
                                    <th>Shipping</th>
                                    <th>Grand Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php
                                    if ($purchase->num_rows > 0) {
                                        $no = 1;
                                        $currencyUSD = '$';
                                        $currencyKHR = '៛';
                                        $g_total = 0;
                                        $total_paid = 0;
                                        $total_balance = 0;
                                        while($pur = $purchase->fetch_assoc()) {
                                            $g_total += $pur['grand_total'];
                                            $total_paid += $pur['paid'];
                                            $total_balance += $pur['balance'];

                                            $grandtotal = $g_total;
                                            $totalpaid = $total_paid;
                                            $totalbalance = $total_balance;

                                            $grandtotal_khr = $g_total * $pur['rate'];
                                            $totalpaid_khr = $total_paid * $pur['rate'];
                                            $totalbalance_khr = $total_balance * $pur['rate'];

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

                                            $status_name = '';
                                            $status_color = '';
                                            if ($pur['status'] == 'approved') {
                                                $status_name = 'Approved';
                                                $status_color = 'badge-approve';
                                            } elseif ($pur['status'] == 'pending') {
                                                $status_name = 'Pending';
                                                $status_color = 'badge-orange';
                                            } elseif ($pur['status'] == 'rejected') {
                                                $status_name = 'Rejected';
                                                $status_color = 'badge-reject';
                                            } elseif ($pur['status'] == 'completed') {
                                                $status_name = 'Completed';
                                                $status_color = 'badge-green';
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
                                        <td class="text-end"><?= formatcurrency_usd($pur['shipping']) ?></td>
                                        <td class="text-end"><?= formatcurrency_usd($pur['grand_total']) ?></td>
                                        <td class="text-end"><?= formatcurrency_usd($pur['paid']) ?></td>
                                        <td class="text-end"><?= formatcurrency_usd($pur['balance']) ?></td>
                                        <td class="text-center"><span class="<?= $payment_status_color ?>"><?= $payment_status_name ?></span></td>
                                        <td class="text-center"><span class="<?= $status_color ?>"><?= $status_name ?></span></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton<?= $pur['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Action
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $pur['id'] ?>">
                                                    <li>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="bi bi-eye pe-2"></i>Purchase Details
                                                        </a>
                                                    </li>
                                                    <?php if($pur['status'] !== 'rejected' && $pur['status'] !== 'completed') { ?>
                                                        <?php if($pur['status'] !== 'approved') { ?>
                                                            <li>
                                                                <a href="edit_purchase.php?id=<?= $pur['id'] ?>" class="dropdown-item">
                                                                    <i class="bi bi-pencil-square pe-2"></i>Edit Purchase
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if($pur['status'] !== 'pending') { ?>
                                                            <?php if ((float)$pur['grand_total'] !== (float)$pur['paid']) { ?>
                                                                <li>
                                                                    <a class="dropdown-item add-payment"
                                                                        href="#" 
                                                                        data-id="<?= $pur['id'] ?>" 
                                                                        data-amount="<?= $pur['grand_total'] ?>" 
                                                                        data-paid="<?= $pur['paid'] ?>"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#addPaymentModal">
                                                                        <i class="bi bi-plus-circle pe-2"></i>Add Payment
                                                                    </a>
                                                                </li>
                                                            <?php } if((float)$pur['paid'] > 0) { ?>
                                                                <li>
                                                                    <a class="dropdown-item add-payback"
                                                                        href="#" 
                                                                        data-id="<?= $pur['id'] ?>" 
                                                                        data-amount="<?= $pur['grand_total'] ?>" 
                                                                        data-paid="<?= $pur['paid'] ?>"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#addPaybackModal">
                                                                        <i class="bi bi-skip-backward pe-2"></i>Add Payback
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                            <li>
                                                                <a class="dropdown-item" href="receive_items.php">
                                                                    <i class="bi bi-truck pe-2"></i>Receive Items
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if($pur['status'] !== 'approved') { ?>
                                                            <li>
                                                                <a class="dropdown-item purchase-approve" href="#" data-id="<?= $pur['id'] ?>" data-bs-toggle="modal" data-bs-target="#confirmationModal">
                                                                    <i class="bi bi-check2 text-success pe-2"></i>Approve
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if(($pur['status'] !== 'pending') && ((float)$pur['paid'] == 0) ) { ?>
                                                            <li>
                                                                <a class="dropdown-item purchase-unapprove" href="#" data-id="<?= $pur['id'] ?>" data-bs-toggle="modal" data-bs-target="#unapproveModal">
                                                                    <i class="bi bi-x-lg text-danger pe-2"></i>Unapprove
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if($pur['status'] !== 'approved') { ?>
                                                            <li>
                                                                <a class="dropdown-item purchase-reject" href="#" data-id="<?= $pur['id'] ?>" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                                    <i class="bi bi-x-octagon-fill text-danger pe-2"></i>Reject
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php if($isAdmin) { ?>
                                                        <li>
                                                            <a class="dropdown-item update-status" href="#" data-id="<?= $pur['id'] ?>" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                                                <i class="bi bi-check2-all fs-5 text-success pe-2"></i>Update Status
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if($pur['status'] !== 'completed') { ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger fw-bold delete-btn" href="#" data-id="<?= $pur['id'] ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                                <i class="bi bi-trash pe-2"></i>Delete
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    $no++;
                                    } 
                                } else { ?>
                                    <tr><td colspan='11' class='text-center text-danger'>No purchase found.</td></tr>
                                <?php } ?>
                            </tbody>
                            <tfoot class="d-none">
                                <tr class="bg-light">
                                    <td colspan="6" class="text-end fw-bold">Totals USD </td>
                                    <td class="text-end"><strong><?= formatcurrency_usd($grandtotal) ?></strong></td>
                                    <td class="text-end"><strong><?= formatcurrency_usd($totalpaid) ?></strong></td>
                                    <td class="text-end"><strong><?= formatcurrency_usd($totalbalance) ?></strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="6" class="text-end fw-bold">Totals KHR </td>
                                    <td class="text-end"><strong><?= formatcurrency_khr($grandtotal_khr) ?></strong></td>
                                    <td class="text-end"><strong><?= formatcurrency_khr($totalpaid_khr) ?></strong></td>
                                    <td class="text-end"><strong><?= formatcurrency_khr($totalbalance_khr) ?></strong></td>
                                    <td></td>
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

    <!-- Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="update_status_id" id="update_status_id" value="">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="update_status">Status</label>
                                    <select class="form-select" id="update_status" name="status" aria-label="Select Display">
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_status" value="1" class="btn btn-primary">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Payment -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="payment_id" id="payment_id" value="">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="payment_date">Date</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= date('Y-m-d') ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="payment_amount">
                                        Amount (<span class="fw-bold">$</span><span class="fw-bold total_amount">0.00</span>)
                                    </label>
                                    <input type="number" 
                                        class="form-control payment_amount"
                                        id="payment_amount" 
                                        name="payment_amount" 
                                        step="0.01"
                                        min="0"
                                        aria-label="Payment Amount"
                                    >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method" aria-label="Select Display">
                                        <option value="cash">Cash</option>
                                        <option value="visa_card">Visa Card</option>
                                        <option value="khqr">KHQR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="payment_note">Note</label>
                                    <textarea class="form-control" id="payment_note" name="payment_note" aria-label="Payment Note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_payment" value="1" class="btn btn-primary">
                            Pay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Payback -->
    <div class="modal fade" id="addPaybackModal" tabindex="-1" aria-labelledby="addPaybackModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaybackModalLabel">Add Payback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="payback_id" id="payback_id" value="">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="payback_amount">
                                        Amount (<span class="fw-bold">$</span><span class="fw-bold total_payback">0.00</span>)
                                    </label>
                                    <input type="number" 
                                        class="form-control payback_amount"
                                        id="payback_amount" 
                                        name="payback_amount"
                                        step="0.01"
                                        min="0"
                                    >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="payment_note">Note</label>
                                    <textarea class="form-control" id="payment_note" name="payment_note" aria-label="Payment Note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_payback" value="1" class="btn btn-primary">
                            Pay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Approval -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="update_id" id="update_id" value="">
                        Are you sure you want to perform this action?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="purchase_approve" value="1" class="btn btn-primary">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Unapproval -->
    <div class="modal fade" id="unapproveModal" tabindex="-1" aria-labelledby="unapproveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unapproveModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="unapprove_id" id="unapprove_id" value="">
                        Are you sure you want to <span class="text-danger fw-bold">unapprove</span> this purchase?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="purchase_unapprove" value="1" class="btn btn-primary">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="reject_id" id="reject_id" value="">
                        Are you sure you want to <span class="text-danger fw-bold">reject</span> this purchase? <br>
                        <span class="text-danger fw-bold">Your action cannot be reversed*</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="purchase_reject" value="1" class="btn btn-outline-primary">
                            Yes, Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="delete_id" id="delete_id" value="">
                        Are you sure you want to <span class="text-danger fw-bold">delete</span> this purchase? <br>
                        <span class="text-danger fw-bold">Your action cannot be reversed*</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="purchase_delete" value="1" class="btn btn-outline-danger">
                            Yes, Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Purchase Invoice Modal -->
    <div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Purchase Details - <span id="modal_purchase_id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="already-paid"><img src="../admin/img/bg-paid.png" class="paid-stamp"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-left w-50 ps-4"><img src="../admin/img/logo1.png" style="width: 40%;border-radius: 50%;"></div>
                        <div class="text-center" style="line-height: 20px;width: 100%;">
                            <div class="fs-4 fw-bold" style="font-family: 'Khmer Moul', sans-serif;"><strong><?= $companies['local_name'] ?></strong></div>
                            <div class="fs-5"><strong><?= $companies['name'] ?></strong></div>
                            <div class="fs-5"><small>Tel: <?= $companies['phone'] ?></small></div>
                            <div class="fs-6"><smal><?= $companies['local_address'] ?></smal></div>
                        </div>
                        <div class="text-right w-50"></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div><strong>Date: </strong> <span id="modal_purchase_date"></span></div>
                            <div><strong>Reference: </strong> <span id="modal_purchase_ref"></span></div>
                            <div><strong>Warehouse: </strong> <span id="modal_purchase_warehouse"></span></div>
                            <div><strong>Payment Note: </strong> <span id="modal_purchase_payment_note"></span></div>
                        </div>
                        <div>
                            <div><strong>Supplier: </strong> <span id="modal_purchase_supplier"></span></div>
                            <div><strong>Supplier Tel: </strong> <span id="modal_purchase_supplier_tel"></span></div>
                            <div><strong>Paid By: </strong> <span class="text-uppercase" id="modal_purchase_paid_by"></span></div>
                            <div><strong>Note: </strong> <span id="modal_purchase_note"></span></div>
                        </div>
                    </div>
                    <div class="text-center fs-5 fw-bold" style="font-family: 'Khmer OS Moul', sans-serif;">Purchase Invoice / វិក្កយបត្របញ្ជាទិញ</div>
                    <hr class="hr-line">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="modal_items_table">
                            <thead class="bg-secondary text-light text-center">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Items Name</th>
                                    <th class="text-center">Cost</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center" width="16%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="modal_items_body"></tbody>
                            <tfoot>
                                <tr>
                                    <?php 
                                        $policy_note = $companies['note'];
                                        $policy_note = nl2br(str_replace(" - ", "\n", $policy_note));
                                    ?>
                                    <td colspan="3" rowspan="7" style="vertical-align:top;border-bottom: 1px solid transparent;border-left: none;font-size: 10px;">
                                        <?= $policy_note ?>
                                    </td>
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Subtotal</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_grand_subtotal"></td>
                                </tr>
                                <tr class="display-shipping">
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Shipping</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_grand_shipping"></td>
                                </tr>
                                <tr class="display-discount">
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Discount</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_grand_discount"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Exchange Rate</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_grand_rate"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Grand Total USD</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_grand_total_usd"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Grand Total KHR</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_grand_total_khr"></td>
                                </tr>
                                <tr class="display-paid">
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Paid</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_paid_total"></td>
                                </tr>
                                <tr class="display-paid">
                                    <td colspan="3" style="text-align: right;font-weight: 700;">Balance</td>
                                    <td style="text-align: right;font-weight: 700;" id="modal_balance_total"></td>
                                </tr>
                            </tfoot>
                        </table>
                        <div><img src="../admin/img/qr-code.jpg" class="img-thumbnail d-none"></div>
                        <table class="w-100" id="table_footer">
                            <tfoot>
                                <tr>
                                    <td class="text-center">Prepared By</td>
                                    <td class="text-center">Checking By</td>
                                    <td class="text-center">Delivery By</td>
                                </tr>
                                <tr>
                                    <td class="text-center line">
                                        <div class="sign_stamp">
                                            <div class="approval">
                                                <img src="../admin/img/signature.png" class="sign">
                                                <img src="../admin/img/stamp1.png" class="stamp">
                                            </div>
                                            <div class="zip-line">____________________________</div>
                                        </div>
                                    </td>
                                    <td class="text-center line">
                                        <div class="zip-line">____________________________</div>
                                    </td>
                                    <td class="text-center line">
                                        <div class="zip-line">____________________________</div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="flex-wrap: nowrap;">
                    <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Close</button>
                    <button id="printPurchase" class="btn btn-success w-100">Print</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $('.delete-btn').click(function() {
                var d_id = $(this).data('id');
                $('#delete_id').val(d_id);
            });

            // approval/unapproval/rejection confirmation modal
            $('.update-status').on('click', function() {
                var us_id = $(this).data('id');
                $('#update_status_id').val(us_id);
            });

            $('.purchase-approve').on('click', function() {
                var ap_id = $(this).data('id');
                $('#update_id').val(ap_id);
            });

            $('.purchase-unapprove').on('click', function() {
                var up_id = $(this).data('id');
                $('#unapprove_id').val(up_id);
            });

            $('.purchase-reject').on('click', function() {
                var rj_id = $(this).data('id');
                $('#reject_id').val(rj_id);
            });

            // add payment
            $(document).on('click', '.add-payment', function() {
                var payment_id = $(this).data('id');
                var total_amount = ((parseFloat($(this).data('amount')) || 0) - (parseFloat($(this).data('paid')) || 0));

                $('#payment_id').val(payment_id);
                $('.total_amount').data('original-amount', total_amount).text(total_amount.toFixed(2));
                $('.payment_amount').val('');
            });

            $('.payment_amount').on('input', function(){
                var $totalAmount = $('.total_amount');
                var originalTotal = parseFloat($totalAmount.data('original-amount')) || 0;
                var paymentAmount = parseFloat($(this).val()) || 0;

                if (paymentAmount > originalTotal) {
                    paymentAmount = originalTotal;
                    $(this).val(originalTotal.toFixed(2));
                }

                var remainingAmount = originalTotal - paymentAmount;
                $totalAmount.text(remainingAmount.toFixed(2));
            });

            // add payback
            $(document).on('click', '.add-payback', function() {
                var payback_id = $(this).data('id');
                var total_payback = (parseFloat($(this).data('paid')) || 0);

                $('#payback_id').val(payback_id);
                $('.total_payback').data('original-amount-payment', total_payback).text(total_payback.toFixed(2));
                $('.payback_amount').val('');
            });

            $('.payback_amount').on('input', function(){
                var $totalAmount = $('.total_payback');
                var originalTotal = parseFloat($totalAmount.data('original-amount-payment')) || 0;
                var paymentAmount = parseFloat($(this).val()) || 0;

                if (paymentAmount > originalTotal) {
                    paymentAmount = originalTotal;
                    $(this).val(originalTotal.toFixed(2));
                }

                var remainingAmount = originalTotal - paymentAmount;
                $totalAmount.text(remainingAmount.toFixed(2));
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

                var displayPaid = $(this).data('paid');
                if(!displayPaid || displayPaid === 0 || displayPaid === '0') {
                    $('.display-paid').addClass('d-none');
                } else {
                    $('.display-paid').removeClass('d-none');
                }
                
                var purchaseId = $(this).data('id');
                // fetch details
                $.post('purchase_details.php', { purchase_id: purchaseId }, function(resp) {
                    if (!resp.success) {
                        alert(resp.error || 'Cannot load purchase details');
                        return;
                    }
                    var p = resp.purchase;
                    var items = resp.items || [];

                    var isAdmin = p.status;
                    if(isAdmin == 'approved') {
                        $('.approval').removeClass('d-none');
                    } else {
                        $('.approval').addClass('d-none');
                    }

                    var isShipping = p.shipping;
                    if(isShipping == 0) {
                        $('.display-shipping').addClass('d-none');
                    } else {
                        $('.display-shipping').removeClass('d-none');
                    }

                    var isDiscount = p.discount;
                    if(isDiscount == 0 || isDiscount == NULL){
                        $('.display-discount').addClass('d-none');
                    } else {
                        $('.display-discount').removeClass('d-none');
                    }

                    var isPaid = p.balance;
                    if(isPaid == 0) {
                        $('.already-paid').removeClass('d-none');
                    } else {
                        $('.already-paid').addClass('d-none');
                    }

                    // populate header info
                    $('#modal_purchase_id').text(p.id);
                    $('#modal_purchase_ref').text(p.reference || '');
                    $('#modal_purchase_date').text(new Date(p.create_date).toLocaleDateString());
                    $('#modal_purchase_supplier').text(p.supplier_name || '');
                    $('#modal_purchase_supplier_tel').text(p.tel || '');
                    $('#modal_purchase_warehouse').text(p.warehouse || '');
                    $('#modal_purchase_paid_by').text(p.payment_method || '');
                    $('#modal_purchase_note').text(p.note || '');
                    $('#modal_purchase_payment_note').text(p.payment_note || '');

                    // populate items
                    var $body = $('#modal_items_body').empty();
                    var grand = 0;
                    items.forEach(function(it, idx) {
                        var cost = parseFloat(it.cost) || 0;
                        var qty = parseInt(it.quantity) || 0;
                        var total = cost * qty;
                        grand += total;
                        var row = '<tr>' +
                                        '<td style="text-align: center;">' + (idx + 1) + '</td>' +
                                        '<td style="text-align: center;">' + $('<div>').text(it.product_code).html() + '</td>' +
                                        '<td>' + $('<div>').text(it.product_name).html() + '</td>' +
                                        '<td style="text-align: right;">$ ' + cost.toFixed(2) + '</td>' +
                                        '<td style="text-align: center;">' + qty + '</td>' +
                                        '<td style="text-align: center;">' + $('<div>').text(it.unit).html() + '</td>' +
                                        '<td style="text-align: right;">$ ' + total.toFixed(2) + '</td>' +
                                    '</tr>';
                        $body.append(row);
                    });
                    $('#modal_grand_shipping').text('$ ' + (parseFloat(p.shipping)).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}));
                    $('#modal_grand_subtotal').text('$ ' + (parseFloat(p.grand_total) || grand).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}));
                    // $('#modal_grand_total_usd').text('$ ' + (parseFloat(p.grand_total + p.shipping) || grand + p.shipping).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}));
                    $('#modal_grand_total_usd').text('$ ' + (((parseFloat(p.grand_total) || 0) + (parseFloat(p.shipping) || 0)) || (grand + (parseFloat(p.shipping) || 0))).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    // $('#modal_grand_total_khr').text('៛ ' + (((parseFloat(p.grand_total) || 0) + (parseFloat(p.shipping) || 0) * p.rate) || (grand + (parseFloat(p.shipping) || 0) * p.rate)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#modal_grand_total_khr').text('៛ ' + ((((parseFloat(p.grand_total) || parseFloat(grand) || 0) + (parseFloat(p.shipping) || 0)) * (parseFloat(p.rate) || 0))).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}));
                    // $('#modal_grand_total_khr').text('៛ ' + (parseFloat(p.grand_total * p.rate) || grand * p.rate).toLocaleString(undefined, {minimumFractionDigits: 0,maximumFractionDigits: 0}));
                    $('#modal_grand_rate').text('៛ ' + (parseFloat(p.rate)).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}));
                    $('#modal_paid_total').text('$ ' + (parseFloat(p.paid)).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}));
                    $('#modal_balance_total').text('$ ' + ((parseFloat(p.grand_total) || grand) - (parseFloat(p.paid))).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}));
                    $('#purchase_note').text(p.note);

                    var purchaseModal = new bootstrap.Modal(document.getElementById('purchaseModal'));
                    purchaseModal.show();

                    // print handler
                    $('#printPurchase').off('click').on('click', function() {
                        // var w = window.open('', '_blank');
                        var html = '<html><head><title>Purchase #' + p.id + '</title>';
                        html += `<style>
                                    @page { size: A5; margin: 5mm; } 
                                    * {
                                        -webkit-print-color-adjust: exact !important;
                                        print-color-adjust: exact !important;
                                    }
                                    body { 
                                        font-family: Arial, sans-serif; 
                                        font-size:12px; 
                                        padding-top: 10px;
                                    } 
                                    table { 
                                        width:100%;
                                        border-collapse:collapse;
                                        font-size: 10px;
                                    } 
                                    th, td { 
                                        border:1px solid #8e8e8e;
                                        padding: 5px;
                                        text-align: left;
                                    } 
                                    th { 
                                        background: #a4a4a4 !important;
                                        text-align: center;
                                        -webkit-print-color-adjust: exact;
                                        print-color-adjust: exact;
                                    } 
                                    table#table_footer{
                                        width:100%;
                                        font-size: 10px;
                                        margin-top: 15px;
                                        border: none;
                                    } 
                                    table#table_footer > tfoot > tr > td{
                                        border: none;
                                        text-align: center;
                                    } 
                                    table#table_footer > tfoot > tr> td.line{
                                        padding-top: 35px;
                                    }
                                    .sign_stamp {
                                        position: relative;
                                    }
                                    img.sign {
                                        width: 60%;
                                        position: absolute;
                                        left: 100px;
                                        bottom: -20px;
                                    }
                                    img.stamp {
                                        width: 25%;
                                        position: absolute;
                                        left: 100px;
                                        bottom: 0px;
                                    }
                                    img.print_logo {
                                        width: 20%;
                                        border-radius: 50%;
                                    }
                                    img.paid-stamp {
                                        width: 15%;
                                        position: absolute;
                                        left: 540px;
                                        top: 180px;
                                        right: 0;
                                        bottom: 0;
                                        z-index: 9999;
                                        opacity: 0.7;
                                    }
                                    .line div.zip-line {
                                        padding-top: 20px;
                                    }
                                    .d-none {
                                        display: none !important;
                                    }
                                    hr.hr-line {
                                        border: 3px double #000000 !important;
                                        margin-top: 0px;
                                        margin-bottom: 5px;
                                        background: transparent;
                                        opacity: 1;
                                    }
                                    @media print {
                                        * {
                                            -webkit-print-color-adjust: exact !important;
                                            print-color-adjust: exact !important;
                                        }
                                        th { 
                                            background-color: #a4a4a4 !important;
                                        } 
                                        img.stamp {
                                            width: 60%;
                                            left: -1rem;
                                            bottom: 0px;
                                        }
                                        img.sign {
                                            width: 130%;
                                            left: -0.5rem;
                                            bottom: -15px;
                                        }
                                        img.print_logo {
                                            width: 40%;
                                        }
                                        img.paid-stamp {
                                            width: 15%;
                                            left: 240px;
                                            top: 180px;
                                        }
                                    }
                                </style>`;
                        html += '</head><body>';
                        if(isPaid == 0) {
                            html += `
                                <div class="already-paid">
                                    <img src="../admin/img/bg-paid.png" class="paid-stamp">
                                </div>
                            `;
                        }
                        html += `<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom: 0.5rem">
                                    <div style="text-align: left;width: 50%;padding-left: 30px;"><img src="../admin/img/logo1.png" class="print_logo"></div>
                                    <div style="text-align:center;line-height: 14px;width: 100%;">
                                        <div style="font-size: 1.5rem;font-family: \'Khmer Moul\', sans-serif;"><strong>`+ (p.company_name_kh || '') +`</strong></div>
                                        <div class="fs-5"><strong>`+ (p.company_name_en || '') +`</strong></div>
                                        <div class="fs-6"><small>VAT: `+ (p.vat || '') +`</small></div>
                                        <div class="fs-6"><small>`+ (p.address_kh || '') +`</small></div>
                                    </div>
                                    <div style="text-align: right;width: 50%;"></div>
                                </div>`;
                        html += `<div style="display:flex;align-items:center;justify-content:space-between;">
                                    <div>
                                        <div><strong>Date: </strong>` + new Date(p.create_date).toLocaleDateString() + `</div>
                                        <div><strong>Reference: </strong>` + (p.reference||'') + `</div>
                                        <div><strong>Warehouse: </strong>` + (p.warehouse||'') + `</div>
                                        <div><strong>Payment Note: </strong>` + (p.payment_note||'') + `</div>
                                    </div>
                                    <div>
                                        <div><strong>Supplier: </strong>` + (p.supplier_name||'') + `</div>
                                        <div><strong>Supplier Tel: </strong>` + (p.tel||'') + `</div>
                                        <div><strong>Paid By: </strong><span style="text-transform: uppercase;">` + (p.payment_method||'') + `</span></div>
                                        <div><strong>Note: </strong>` + (p.note||'') + `</div>
                                    </div>
                                </div>`;
                        html += '<div style="text-align: center; font-size: 1.2em; font-weight: bold; font-family: \'Khmer Moul\', sans-serif;">Purchase Invoice / វិក្កយបត្របញ្ជាទិញ</div>';
                        html += '<hr class="hr-line">';
                        html += document.getElementById('modal_items_table').outerHTML;
                        html += document.getElementById('table_footer').outerHTML;
                        // html += '<script>window.print();<\/script>';
                        html += '</body></html>';
                        // w.document.write(html);
                        // w.document.close();

                        var iframe = document.createElement('iframe');

                        iframe.style.position = 'fixed';
                        iframe.style.right = '0';
                        iframe.style.bottom = '0';
                        iframe.style.width = '0';
                        iframe.style.height = '0';
                        iframe.style.border = '0';

                        document.body.appendChild(iframe);
                        var iframeDoc = iframe.contentWindow.document;

                        iframeDoc.open();
                        iframeDoc.write(html);
                        iframeDoc.close();

                        iframe.onload = function () {
                            setTimeout(function () {
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                                // Remove iframe after printing
                                setTimeout(function () {
                                    document.body.removeChild(iframe);
                                }, 1000);
                            }, 200);
                        };
                    });

                }, 'json').fail(function() {
                    alert('Request failed');
                });
            });
        });
    </script>
</body>
</html>