<?php 
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";     
    $dbname = "foodmart";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // Authorization
    if ($_SESSION['user_role'] !== 'admin') {
        $_SESSION['message'] = 'Access denied...!';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }

    // Update User
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUser']) == TRUE) {
        
        $update_id = $_POST['update_id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $encrypted_password = md5($password);
        $age = $_POST['age'];
        $phone = $_POST['phone'];
        $gender = $_POST['gender'];
        $status = $_POST['status'];

        // role id and name
        $role_id = (int)$_POST['role_id'];
        $stmtRole = $conn->prepare("SELECT name FROM user_roles WHERE id = ?");
        $stmtRole->bind_param("i", $role_id);
        $stmtRole->execute();
        $stmtRole->bind_result($user_role);
        $stmtRole->fetch();
        $stmtRole->close();

        // Fetch old image path
        $stmtImage = $conn->prepare("SELECT image FROM users WHERE id = ?");
        $stmtImage->bind_param("i", $update_id);
        $stmtImage->execute();
        $stmtImage->bind_result($oldImagePath);
        $stmtImage->fetch();
        $stmtImage->close();
        
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
                $stmt = $conn->prepare("UPDATE users SET
                                            firstname = ?, 
                                            lastname = ?, 
                                            username = ?, 
                                            password = ?, 
                                            age = ?, 
                                            phone = ?, 
                                            gender = ?, 
                                            status = ?, 
                                            image = ?,
                                            user_role = ?,
                                            role_id = ?
                                        WHERE id = ?
                                    ");
                $stmt->bind_param("ssssississi",
                                    $firstname, 
                                    $lastname, 
                                    $username, 
                                    $encrypted_password, 
                                    $age, 
                                    $phone, 
                                    $gender, 
                                    $status,
                                    $encryptedName,
                                    $user_role,
                                    $role_id,
                                    $update_id
                                );
            } else {
                $_SESSION['message'] = 'Error uploading image!';
                $_SESSION['message_type'] = 'danger';
                header('Location: user.php');
                exit();
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET
                                            firstname = ?, 
                                            lastname = ?, 
                                            username = ?, 
                                            password = ?, 
                                            age = ?, 
                                            phone = ?, 
                                            gender = ?, 
                                            status = ?,
                                            user_role = ?,
                                            role_id = ?
                                        WHERE id = ?
                                    ");
            $stmt->bind_param("ssssississi",
                                $firstname, 
                                $lastname, 
                                $username, 
                                $encrypted_password, 
                                $age, 
                                $phone, 
                                $gender, 
                                $status,
                                $user_role,
                                $role_id,
                                $update_id
                            );

        }
        if ($stmt->execute()) {
            $_SESSION['message'] = 'User role added successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: user.php');
        exit();
    }
    $id = (int) $_GET['id'];
    $edit_user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

    $units = $conn->query("SELECT id, name FROM units");
    $userRoles = $conn->query("SELECT id, name FROM user_roles");
    
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php include "include/header.php" ?>
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
                        <h5 class="mb-0 fw-bold text-title">Edit User</h5>
                    </div>
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                            <?=$_SESSION['message']?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <form action="edit_user.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <input type="hidden" name="update_id" value="<?= $edit_user['id'] ?>">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?= $edit_user['firstname'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?= $edit_user['lastname'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= $edit_user['username'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" value="<?= $edit_user['password'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="number" class="form-control" id="age" name="age" value="<?= $edit_user['age'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= $edit_user['phone'] ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Gender">Gender</label>
                                    <select class="form-select" id="Gender" name="gender" aria-label="Select Gender">
                                        <option value="male" <?= $edit_user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= $edit_user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-select" id="role" name="role_id">
                                        <option value="">Select Role</option>
                                        <?php
                                            while($role = $userRoles->fetch_assoc()) {
                                                $selected = $role['id'] == $edit_user['role_id'] ? 'selected' : '';
                                                echo "<option value='" . $role['id'] . "' " . $selected . ">" . htmlspecialchars($role['name']) . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-select" name="status">
                                        <option value="1" <?= $edit_user['status'] == 1 ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= $edit_user['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" class="mb-1">
                                    <label for="discount">User Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="updateUser" value="Update" class="btn btn-success">
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
                $('#categorySelect').select2({
                    theme: 'bootstrap-5',           // matches Bootstrap styling
                    placeholder: '-- Choose a category --',
                    allowClear: true,               // shows an X to clear selection
                    width: '100%'                   // full width of the container
                });

                $('#role').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Choose a role --',
                    allowClear: true,
                    width: '100%'
                });
            });
        });
    </script>
</body>
</html>