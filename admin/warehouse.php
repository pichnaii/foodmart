<?php
    require_once 'include/dbconnection.php';

    // Add currency
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addWarehouse']) == TRUE){
        $code         = $_POST['code'];
        $name         = $_POST['name'];
        $local_name   = $_POST['local_name'];
        $address      = $_POST['address'];
        $note         = $_POST['note'];
        $created_date = $_POST['created_date'];

        $stmt = $conn->prepare("INSERT INTO warehouse 
                                (
                                    code, 
                                    name, 
                                    local_name, 
                                    address, 
                                    note, 
                                    created_date
                                ) VALUES (?, ?, ?, ?, ?, ?)
                            ");
        $stmt->bind_param("ssssss", 
                            $code, 
                            $name, 
                            $local_name, 
                            $address, 
                            $note, 
                            $created_date
                        );

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Warehouse added Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Warehouse added Unsuccessfully! Error: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: warehouse.php');
        exit();
    }

    // Update user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateWarehouse']) == TRUE) {
        $update_id    = $_POST['update_id'];
        $code         = $_POST['code'];
        $name         = $_POST['name'];
        $local_name   = $_POST['local_name'];
        $address      = $_POST['address'];
        $note         = $_POST['note'];
        $created_date = $_POST['created_date'];

        $stmt = $conn->prepare("UPDATE warehouse SET 
                                    code = ?, 
                                    name = ?, 
                                    local_name = ?, 
                                    address = ?, 
                                    note = ?, 
                                    created_date = ? 
                                WHERE id = ?
                            ");
        $stmt->bind_param("ssssssi", 
                            $code, 
                            $name, 
                            $local_name, 
                            $address, 
                            $naote, 
                            $created_date, 
                            $update_id
                        );
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Warehouse updated Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Warehouse update Unsuccessful! Error: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
    
        header('Location: warehouse.php');
        exit();
    }

    // Delete Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) == TRUE) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM warehouse WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
    
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Warehouse deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete Warehouse!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: warehouse.php');
        exit();
    }

    $warehouse = $conn->query("SELECT * FROM warehouse ORDER BY created_date DESC");

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
                        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addWarehouse">
                            <i class="fas fa-plus"></i> Add Warehouse
                        </button>
                        <h5 class="mb-0 fw-bold text-title">Warehouse List</h5>
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
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Local Name</th>
                                    <th>Address</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php
                                    if ($warehouse->num_rows > 0) {
                                        $no = 1;
                                        while($row = $warehouse->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($row['created_date'])) ?></td>
                                    <td class="text-center"><?= $row['code'] ?></td>
                                    <td class="text-center"><?= $row['name'] ?></td>
                                    <td class="text-center"><?= $row['local_name'] ?></td>
                                    <td class="text-center"><?= $row['address'] ?></td>
                                    <td class="text-center"><?= $row['note'] ?></td>
                                    <td class="text-center">
                                        <a class="edit-btn" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-created_date="<?= $row['created_date'] ?>" 
                                            data-code="<?= $row['code'] ?>" 
                                            data-name="<?= $row['name'] ?>" 
                                            data-local_name="<?= $row['local_name'] ?>" 
                                            data-address="<?= $row['address'] ?>" 
                                            data-note="<?= $row['note'] ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#EditWarehouse">
                                            <i class="bi bi-pencil-square cursor-pointer fs-4"></i>
                                        </a>
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

    <!-- Add Warehouse -->
    <div class="modal fade" id="addWarehouse" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="warehouse.php" method="post" enctype="multipart/form-data">
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
                                    <label for="code">Code</label>
                                    <input type="text" class="form-control" name="code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="local_name">Local Name</label>
                                    <input type="text" class="form-control" name="local_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" name= "address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="note">Description</label>
                                    <textarea class="form-control" name= "note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="addWarehouse" value="Submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier -->
    <div class="modal fade" id="EditWarehouse" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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
                                    <label for="edit_code">Code</label>
                                    <input type="text" class="form-control" id="edit_code" name="code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_name">Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_local_name">Local Name</label>
                                    <input type="text" class="form-control" id="edit_local_name" name="local_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit-address">Address</label>
                                    <textarea class="form-control" id="edit_address" name= "address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit-note">Description</label>
                                    <textarea class="form-control" id="edit_note" name= "note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateWarehouse" class="btn btn-primary">Update</button>
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
                var code = $(this).data('code');
                var name = $(this).data('name');
                var local_name = $(this).data('local_name');
                var address = $(this).data('address');
                var note = $(this).data('note');
            
                $('#update_id').val(id);
                $('#edit_created_date').val(date);
                $('#edit_code').val(code);
                $('#edit_name').val(name);
                $('#edit_local_name').val(local_name);
                $('#edit_address').val(address);
                $('#edit_note').val(note);
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