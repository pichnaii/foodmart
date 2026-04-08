<?php 
    include 'include/dbconnection.php';
    // Add User Role
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUserRole']) == TRUE){
        $code = $_POST['code'];
        $name = $_POST['name'];
        $created_date = $_POST['created_date'];
        $updated_date = $_POST['updated_date'];
        $description = $_POST['description'];
        $status = $_POST['status'];

        if (isset($_FILES['image'])) {
            $image = $_FILES['image'];
            $encryptedName = md5(time() . $image['name']) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetDir = "images/uploads/users/";
            $targetFilePath = $targetDir . $encryptedName;

            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                $stmt = $conn->prepare("INSERT INTO user_roles 
                                                (
                                                    code, 
                                                    name, 
                                                    created_date, 
                                                    updated_date, 
                                                    description, 
                                                    status, 
                                                    user_image
                                                ) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", 
                                    $code, 
                                    $name, 
                                    $created_date, 
                                    $updated_date, 
                                    $description, 
                                    $status, 
                                    $encryptedName
                                );

                if ($stmt->execute()) {
                    $_SESSION['message'] = 'User role added successfully!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = "Error123: " . $stmt->error;
                    $_SESSION['message_type'] = 'danger';
                }
                $stmt->close();
                header('Location: user_permission.php');
                exit();

            } else {
                $stmt = $conn->prepare("INSERT INTO user_roles 
                                                (
                                                    code, 
                                                    name, 
                                                    created_date, 
                                                    updated_date, 
                                                    description, 
                                                    status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", 
                                    $code, 
                                    $name, 
                                    $created_date, 
                                    $updated_date, 
                                    $description, 
                                    $status
                                );
            }
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = 'User role added successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: user_permission.php');
        exit();
    }
    
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
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="mb-0 fw-bold text-title">Add Products</h5>
                    </div>
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <form action="add_user_role.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date" class="mb-1">Created Date</label>
                                    <input type="date" class="form-control" name="created_date" value="<?= date('Y-m-d') ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 d-none">
                                <div class="form-group">
                                    <label for="date" class="mb-1">Updated Date</label>
                                    <input type="date" class="form-control" name="updated_date" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="code" class="mb-1">Code <span class="text-danger fw-bold">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="code" name="code" placeholder="General By System or Manual...">
                                    <button type="button" class="btn btn-success" id="generateCode">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="mb-1">User Roles <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control" name="name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description" class="mb-1">Description</label>
                                    <input type="text" class="form-control" name="description">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-select" name="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" class="mb-1">
                                    <label for="discount">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="addUserRole" value="Submit" class="btn btn-success">
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

            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });

            // generate code 
            let codeHistory = [];
            $('#generateCode').on('click', function() {
                // Generate new code
                let randomNum = Math.floor(Math.random() * 100000000);
                let newCode = randomNum.toString().padStart(8, '0');
                
                // Check if code already exists in history (optional)
                if (codeHistory.includes(newCode)) {
                    // If exists, generate a new one
                    generateUniqueCode();
                } else {
                    // Add to history and set value
                    codeHistory.push(newCode);
                    $('#code').val(newCode);
                    
                    // Limit history size (keep last 10)
                    if (codeHistory.length > 10) {
                        codeHistory.shift();
                    }
                }
            });
            function generateUniqueCode() {
                let randomNum = Math.floor(Math.random() * 100000000);
                let newCode = randomNum.toString().padStart(8, '0');
                
                if (codeHistory.includes(newCode)) {
                    generateUniqueCode(); // Recursive until unique
                } else {
                    codeHistory.push(newCode);
                    $('#code').val(newCode);
                }
            }   
            generateUniqueCode();

            $('#productSearch').on('keyup', function() {
                var query = $(this).val().toLowerCase().trim();
                $('table tbody tr').each(function() {
                    var productData = $(this).text().toLowerCase();
                    if (productData.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
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

                $('#unitSelect').select2({
                    theme: 'bootstrap-5',         
                    placeholder: '-- Choose a unit --',
                    allowClear: true,              
                    width: '100%'          
                });
            });
        });
    </script>
</body>
</html>