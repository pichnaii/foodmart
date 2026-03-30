<?php
    require_once 'include/dbconnection.php';

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Add currency
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCurrency']) == TRUE){
        $base_currency = $_POST['base_currency'];
        $currency_code = $_POST['currency_code'];
        $currency_name = $_POST['currency_name'];
        $currency_symbol = $_POST['currency_symbol'];
        $exchange_rate = $_POST['exchange_rate'];
        $created_date = $_POST['created_date'];

        $stmt = $conn->prepare("INSERT INTO currency 
                                (
                                    base_currency, 
                                    currency_code, 
                                    currency_name, 
                                    currency_symbol, 
                                    exchange_rate, 
                                    created_date
                                ) VALUES (?, ?, ?, ?, ?, ?)
                            ");
        $stmt->bind_param("ssssss", 
                            $base_currency, 
                            $currency_code, 
                            $currency_name, 
                            $currency_symbol, 
                            $exchange_rate, 
                            $created_date
                        );

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Currency added Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Currency added Unsuccessfully! Error: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: currency.php');
        exit();
    }

    // Update user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCurrency']) == TRUE) {
        $update_id = $_POST['update_id'];
        $base_currency = $_POST['base_currency'];
        $currency_code = $_POST['currency_code'];
        $currency_name = $_POST['currency_name'];
        $currency_symbol = $_POST['currency_symbol'];
        $exchange_rate = $_POST['exchange_rate'];
        $created_date = $_POST['created_date'];

        $stmt = $conn->prepare("UPDATE currency SET 
                                    base_currency = ?, 
                                    currency_code = ?, 
                                    currency_name = ?, 
                                    currency_symbol = ?, 
                                    exchange_rate = ?, 
                                    created_date = ? 
                                WHERE id = ?
                            ");
        $stmt->bind_param("ssssssi", 
                            $base_currency, 
                            $currency_code, 
                            $currency_name, 
                            $currency_symbol, 
                            $exchange_rate, 
                            $created_date, 
                            $update_id
                        );
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Currency updated Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Currency update Unsuccessful! Error: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
    
        header('Location: currency.php');
        exit();
    }

    // Delete Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) == TRUE) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM currency WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
    
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Currency deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete Currency!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: currency.php');
        exit();
    }

    $currencies = $conn->query("SELECT * FROM currency ORDER BY created_date DESC");

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
                        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addCurrency">
                            <i class="fas fa-plus"></i> Add Currency
                        </button>
                        <h5 class="mb-0 fw-bold text-title">Currency List</h5>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div></div>
                        <div class="input-group w-25">
                            <input type="text" id="supplierSearch" class="form-control w-50" placeholder="Search.....">
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
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Base Currency</th>
                                    <th>Currency Code</th>
                                    <th>Currency Name</th>
                                    <th>Currency Symbol</th>
                                    <th>Exchange Rate</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php
                                    if ($currencies->num_rows > 0) {
                                        $no = 1;
                                        while($row = $currencies->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($row['created_date'])) ?></td>
                                    <td class="text-center"><?= $row['base_currency'] ?></td>
                                    <td class="text-center"><?= $row['currency_code'] ?></td>
                                    <td class="text-center"><?= $row['currency_name'] ?></td>
                                    <td class="text-center"><?= $row['currency_symbol'] ?></td>
                                    <td class="text-center"><?= $row['exchange_rate'] ?></td>
                                    <td class="text-center">
                                        <?php if ($_SESSION['user_role'] === 'admin') { ?>
                                            <a class="edit-btn" 
                                                data-id="<?= $row['id'] ?>" 
                                                data-created_date="<?= $row['created_date'] ?>" 
                                                data-base_currency="<?= $row['base_currency'] ?>" 
                                                data-currency_code="<?= $row['currency_code'] ?>" 
                                                data-currency_name="<?= $row['currency_name'] ?>" 
                                                data-currency_symbol="<?= $row['currency_symbol'] ?>" 
                                                data-exchange_rate="<?= $row['exchange_rate'] ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#EditCurrency">
                                                <i class="bi bi-pencil-square cursor-pointer fs-4"></i>
                                            </a>
                                        <?php } ?>
                                        <a class="delete-btn" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#delete">
                                            <i class="bi bi-trash text-danger cursor-pointer fs-4"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    $no++;
                                    } 
                                } else { ?>
                                    <tr><td colspan='10' class='text-center text-danger'>No suppliers found.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php include "include/footer.php"?>
		</div>
		<?php include "include/foot.php"?>
	</div>

    <!-- Add Currency -->
    <div class="modal fade" id="addCurrency" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Currency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="currency.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" name="created_date" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Base Currency</label>
                                    <input type="text" class="form-control" name="base_currency">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Currency Code</label>
                                    <input type="text" class="form-control" name="currency_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company">Currency Name</label>
                                    <input type="text" class="form-control" name="currency_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Currency Symbol</label>
                                    <input type="text" class="form-control" name="currency_symbol">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Exchange Rate</label>
                                    <input type="text" class="form-control" name="exchange_rate">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="addCurrency" value="Submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier -->
    <div class="modal fade" id="EditCurrency" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="update_id" id="update_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_created_date">Date</label>
                                    <input type="date" class="form-control" id="edit_created_date" name="created_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_base_currency">Base Currency</label>
                                    <input type="text" class="form-control" id="edit_base_currency" name="base_currency">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_currency_code">Currency Code</label>
                                    <input type="text" class="form-control" id="edit_currency_code" name="currency_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_currency_name">Currency Name</label>
                                    <input type="text" class="form-control" id="edit_currency_name" name="currency_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_currency_symbol">Currency Symbol</label>
                                    <input type="text" class="form-control" id="edit_currency_symbol" name="currency_symbol">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_exchange_rate">Exchange Rate</label>
                                    <input type="text" class="form-control" id="edit_exchange_rate" name="exchange_rate">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateCurrency" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var date = $(this).data('created_date');
                var base_currency = $(this).data('base_currency');
                var currency_code = $(this).data('currency_code');
                var currency_name = $(this).data('currency_name');
                var currency_symbol = $(this).data('currency_symbol');
                var exchange_rate = $(this).data('exchange_rate');
            
                $('#update_id').val(id);
                $('#edit_created_date').val(date);
                $('#edit_base_currency').val(base_currency);
                $('#edit_currency_code').val(currency_code);
                $('#edit_currency_name').val(currency_name);
                $('#edit_currency_symbol').val(currency_symbol);
                $('#edit_exchange_rate').val(exchange_rate);
            });

            $('.delete-btn').click(function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });

            // Category Search
            $('#supplierSearch').on('keyup', function() {
                var category = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(category) > -1)
                });
            });
        });
    </script>
</body>
</html>