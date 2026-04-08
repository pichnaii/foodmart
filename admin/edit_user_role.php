<?php 
    require_once 'include/dbconnection.php'; 
    // Update User Role
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUserRole']) == TRUE) {
        $update_id = $_POST['update_id'];
        $code = $_POST['code'];
        $name = $_POST['name'];
        $created_date = $_POST['created_date'];
        $updated_date = $_POST['updated_date'];
        $description = $_POST['description'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("SELECT user_image FROM user_roles WHERE id = ?");
        $stmt->bind_param("i", $update_id);
        $stmt->execute();
        $stmt->bind_result($oldImagePath);
        $stmt->fetch();
        $stmt->close();
    
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $encryptedName = md5(time() . $image['name']) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $targetDir = "images/uploads/users/";
            $targetFilePath = $targetDir . $encryptedName;

            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                if (!empty($oldImagePath)) {
                    $oldFilePath = $targetDir . $oldImagePath;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $stmt = $conn->prepare("UPDATE user_roles SET 
                                            code = ?, 
                                            name = ?, 
                                            created_date = ?, 
                                            updated_date = ?, 
                                            description = ?,
                                            status = ?, 
                                            user_image = ? 
                                        WHERE id = ?
                                    ");
                $stmt->bind_param("sssssssi", 
                                    $code, 
                                    $name, 
                                    $created_date, 
                                    $updated_date,
                                    $description,
                                    $status, 
                                    $encryptedName, 
                                    $update_id
                                );
            } else {
                $_SESSION['message'] = 'Error uploading image!';
                $_SESSION['message_type'] = 'danger';
                header('Location: user_permission.php');
                exit();
            }
        } else {
            $stmt = $conn->prepare("UPDATE user_roles SET 
                                        code = ?, 
                                        name = ?, 
                                        created_date = ?, 
                                        updated_date = ?, 
                                        description = ?,
                                        status = ?
                                    WHERE id = ?
                                ");
            $stmt->bind_param("ssssssi",
                                $code, 
                                $name, 
                                $created_date, 
                                $updated_date,
                                $description,
                                $status,
                                $update_id
                            );
        }
    
        if ($stmt->execute()) {
            $_SESSION['message'] = 'User role updated successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: user_permission.php');
        exit();
    }

    // for editing user role
    $id = (int) $_GET['id'];
    $edit_sql = "SELECT 
                    user_roles.id AS id,
                    user_roles.code AS code,
                    user_roles.name AS name,
                    user_roles.created_date AS created_date,
                    user_roles.updated_date AS updated_date,
                    user_roles.description AS description,
                    user_roles.user_image AS user_image,
                    user_roles.status AS status
                FROM user_roles
                WHERE user_roles.id = $id
            ";
    $user_roles = $conn->query($edit_sql)->fetch_assoc();
    // $user_roles = $conn->query("SELECT * FROM user_roles WHERE id = $id")->fetch_assoc();
    
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
                    <form action="edit_user_role.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <input type="hidden" name="update_id" value="<?= $user_roles['id'] ?>">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date" class="mb-1">Created Date</label>
                                    <input type="date" class="form-control" name="created_date" value="<?= $user_roles['created_date'] ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 d-none">
                                <div class="form-group">
                                    <label for="date" class="mb-1">Updated Date</label>
                                    <input type="date" class="form-control" name="updated_date" value="<?= date('Y-m-d') ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="code" class="mb-1">Code <span class="text-danger fw-bold">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($user_roles['code']) ?>">
                                    <button type="button" class="btn btn-success" id="generateCode">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="mb-1">User Role <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user_roles['name']) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description" class="mb-1">Description</label>
                                    <input type="text" class="form-control" name="description" value="<?= htmlspecialchars($user_roles['description']) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-select" name="status" id="edit_status">
                                        <option value="1" <?= ($user_roles['status'] == 1) ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= ($user_roles['status'] == 0) ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" class="mb-1">
                                    <label for="discount">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" id="imageInput">
                                    <img id="imagePreview"
                                        src="<?= !empty($user_roles['user_image']) 
                                                ? 'images/uploads/users/' . htmlspecialchars($user_roles['user_image']) 
                                                : 'images/uploads/no-image.png' ?>"
                                        class="mt-2 img-thumbnail" 
                                        style="max-width:150px;"
                                    >
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="updateUserRole" value="Update" class="btn btn-success">
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
            function showImagePreview(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    $('#imagePreview').hide();
                }
            }
            $('#imageInput').on('change', function () {
                showImagePreview(this);
            });

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