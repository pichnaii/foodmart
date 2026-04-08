<?php
    require_once 'include/dbconnection.php';

    // Add currency
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCompany']) == TRUE){

        $logo           = $_POST['logo'];
        $code           = $_POST['code'];
        $name           = $_POST['name'];
        $local_name     = $_POST['local_name'];
        $address        = $_POST['address'];
        $local_address  = $_POST['local_address'];
        $phone          = $_POST['phone'];
        $email          = $_POST['email'];
        $vat            = $_POST['vat'];
        $note           = $_POST['note'];
        $created_date   = $_POST['created_date'];
        $updated_date   = $_POST['updated_date'];

        $stmt = $conn->prepare("INSERT INTO company 
                                (
                                    logo,
                                    code, 
                                    name, 
                                    local_name, 
                                    address, 
                                    local_address,
                                    phone,
                                    email,
                                    vat,
                                    note, 
                                    created_date,
                                    updated_date
                                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ");
        $stmt->bind_param("ssssssssssss", 
                            $logo,
                            $code, 
                            $name, 
                            $local_name, 
                            $address,
                            $local_address,
                            $phone,
                            $email,
                            $vat,
                            $note, 
                            $created_date,
                            $updated_date
                        );

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Company added Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Company added Unsuccessfully! Error: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: company.php');
        exit();
    }

    // Update Company
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCompany']) == TRUE) {

        $update_id      = $_POST['update_id'];
        $logo           = $_POST['logo'];
        $code           = $_POST['code'];
        $name           = $_POST['name'];
        $local_name     = $_POST['local_name'];
        $address        = $_POST['address'];
        $local_address  = $_POST['local_address'];
        $phone          = $_POST['phone'];
        $email          = $_POST['email'];
        $vat            = $_POST['vat'];
        $note           = $_POST['note'];
        $updated_date   = $_POST['updated_date'];

        // ✅ Fetch created_date from DB first
        $stmtCheck = $conn->prepare("SELECT created_date FROM company WHERE id = ?");
        $stmtCheck->bind_param("i", $update_id);
        $stmtCheck->execute();
        $stmtCheck->bind_result($created_date);
        $stmtCheck->fetch();
        $stmtCheck->close();

        // ✅ Convert to DateTime for comparison
        $dtCreated = new DateTime($created_date);
        $dtUpdated = new DateTime($updated_date);

        // ✅ Validate: updated_date cannot be before created_date
        if ($dtUpdated < $dtCreated) {
            $_SESSION['message']      = 'Updated date cannot be earlier than created date (' . $dtCreated->format('d/m/Y') . ').';
            $_SESSION['message_type'] = 'danger';
            header('Location: company.php');
            exit();
        }

        $stmt = $conn->prepare("UPDATE company SET 
                                    logo = ?,
                                    code = ?, 
                                    name = ?, 
                                    local_name = ?, 
                                    address = ?,
                                    local_address = ?,
                                    phone = ?,
                                    email = ?,
                                    vat = ?,
                                    note = ?, 
                                    updated_date = ? 
                                WHERE id = ?
                            ");
        $stmt->bind_param("sssssssssssi", 
                            $logo,
                            $code, 
                            $name, 
                            $local_name, 
                            $address, 
                            $local_address,
                            $phone,
                            $email,
                            $vat,
                            $note, 
                            $updated_date,
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
    
        header('Location: company.php');
        exit();
    }

    // Delete Company
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) == TRUE) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM company WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
    
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Company deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete Company!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: company.php');
        exit();
    }

    $company = $conn->query("SELECT * FROM company ORDER BY created_date DESC");

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
                        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addCompany">
                            <i class="fas fa-plus"></i> Add Company
                        </button>
                        <h5 class="mb-0 fw-bold text-title">Company List</h5>
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
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Logo</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Local Name</th>
                                    <th>Address</th>
                                    <th>Local Address</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>VAT</th>
                                    <th>Description</th>
                                    <th>Created Date</th>
                                    <th>Updated Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php
                                    if ($company->num_rows > 0) {
                                        $no = 1;
                                        while($row = $company->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no ?></td>
                                    <td class="text-center"><?= $row['logo'] ?></td>
                                    <td class="text-center"><?= $row['code'] ?></td>
                                    <td class="text-center"><?= $row['name'] ?></td>
                                    <td class="text-center"><?= $row['local_name'] ?></td>
                                    <td class="text-center"><?= $row['address'] ?></td>
                                    <td class="text-center"><?= $row['local_address'] ?></td>
                                    <td class="text-center"><?= $row['phone'] ?></td>
                                    <td class="text-center"><?= $row['email'] ?></td>
                                    <td class="text-center"><?= $row['vat'] ?></td>
                                    <td class="text-center"><?= $row['note'] ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($row['created_date'])) ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($row['updated_date'])) ?></td>
                                    <td class="text-center">
                                        <a class="edit-btn" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-logo="<?= $row['logo'] ?>"
                                            data-code="<?= $row['code'] ?>" 
                                            data-name="<?= $row['name'] ?>" 
                                            data-local_name="<?= $row['local_name'] ?>" 
                                            data-address="<?= $row['address'] ?>" 
                                            data-local_address="<?= $row['local_address'] ?>" 
                                            data-phone="<?= $row['phone'] ?>" 
                                            data-email="<?= $row['email'] ?>" 
                                            data-vat="<?= $row['vat'] ?>" 
                                            data-note="<?= $row['note'] ?>" 
                                            data-updated_date="<?= $row['updated_date'] ?>"
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
                                    <tr><td colspan='14' class='text-center text-danger'>No company found.</td></tr>
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

    <!-- Add Company -->
    <div class="modal fade" id="addCompany" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="company.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Created Date</label>
                                    <input type="date" class="form-control" name="created_date" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 d-none">
                                <div class="form-group">
                                    <label for="date">Updated Date</label>
                                    <input type="date" class="form-control" name="updated_date" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" name="logo" accept="image/*">
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
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vat">VAT</label>
                                    <input type="text" class="form-control" name="vat">
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
                                    <label for="address">Local Dddress</label>
                                    <textarea class="form-control" name="local_address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="note">Description</label>
                                    <textarea class="form-control" name= "note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="addCompany" value="Submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Company -->
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
                            <div class="col-md-6 d-none">
                                <div class="form-group">
                                    <label for="date">Created Date</label>
                                    <input type="date" class="form-control" id="edit_created_date" name="created_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Updated Date</label>
                                    <input type="date" class="form-control" id="edit_updated_date" name="updated_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" class="form-control" id="edit_code" name="code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="local_name">Local Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="local_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="edit_phone" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="edit_email" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vat">VAT</label>
                                    <input type="text" class="form-control" id="edit_vat" name="vat">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="edit_address" name="address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Local Dddress</label>
                                    <textarea class="form-control" id="edit_local_address" name="local_address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="note">Description</label>
                                    <textarea class="form-control" id="edit_note" name="note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateCompany" class="btn btn-primary">Update</button>
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
                var code = $(this).data('code');
                var name = $(this).data('name');
                var local_name = $(this).data('local_name');
                var address = $(this).data('address');
                var local_address = $(this).data('local_address');
                var email = $(this).data('email');
                var phone = $(this).data('phone');
                var vat = $(this).data('vat');
                var note = $(this).data('note');
                var date = $(this).data('updated_date');
            
                $('#update_id').val(id);
                $('#edit_code').val(code);
                $('#edit_name').val(name);
                $('#edit_local_name').val(local_name);
                $('#edit_address').val(address);
                $('#edit_local_address').val(local_address);
                $('#edit_phone').val(phone);
                $('#edit_email').val(email);
                $('#edit_vat').val(vat);
                $('#edit_note').val(note);
                $('#edit_updated_date').val(date);
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