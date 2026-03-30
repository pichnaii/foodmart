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

    // Add user
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser']) == TRUE){
        $user_role = $_POST['user_role'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $age = $_POST['age'];

        // Hash password
        $encrypted_password = md5($password);

        $stmt = $conn->prepare("INSERT INTO users (user_role ,username, password, lastname, firstname, gender, phone, age) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("sss", $user_role, $username, $encrypted_password, $lastname, $firstname, $gender, $phone, $age);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'User added Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'User added Unsuccessfully!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: user.php');
        exit();
    }

    // Update user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUser'])) {
        $update_id = $_POST['update_id'];
        $user_role = $_POST['user_role'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $age = $_POST['age'];

        $encrypted_password = md5($password);

        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, user_role = ?, lastname = ?, firstname = ?, gender = ?, phone = ?, age = ? WHERE id = ?");
        $stmt->bind_param("sssssssii", $username, $encrypted_password, $user_role, $lastname, $firstname, $gender, $phone, $age, $update_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Product updated Successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Product update Unsuccessful!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
    
        header('Location: user.php');
        exit();
    }

    // Delete User
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
    
        if ($stmt->execute()) {
            $_SESSION['message'] = 'User deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'User added Unsuccessfully!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: user.php');
        exit();
    }

    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
    $config_setting = $conn->query('SELECT * FROM configurations')->fetch_assoc();
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
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <?php if($_SESSION['user_role'] == 'admin') { ?>
                            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#add">
                                <i class="fas fa-plus"></i> Add User
                            </button>
                        <?php } ?>
                        <h5 class="mb-0 fw-bold text-title">Users List</h5>
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
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Username</th>
                                    <?php if($_SESSION['user_role'] == 'admin'){?>
                                        <th>Password</th>
                                    <?php } ?>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php
                                    if ($result->num_rows > 0) {
                                        $no = 1;
                                        while($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no ?></td>
                                    <td><?= $row['lastname'] ?></td>
                                    <td><?= $row['firstname'] ?></td>
                                    <td><?= $row['username'] ?></td>
                                    <?php if($_SESSION['user_role'] == 'admin') { ?>
                                        <td class="text-center"><span class="btn-sm btn-danger">********</span></td>
                                    <?php } ?>
                                    <td class="text-center"><span class="text-<?= ($row['gender'] == 'male') ? 'light' : 'dark' ?> text-uppercase btn-sm btn-<?= ($row['gender'] == 'male') ? 'success' : 'warning'?>"><?= $row['gender'] ?></span></td>
                                    <td class="text-center"><?= $row['age'] ?></td>
                                    <td class="text-center"><?= $row['phone'] ?></td>
                                    <td class="text-center"><span class="btn-sm btn-secondary"><?= $row['user_role'] ?></span></td>
                                    <td class="text-center">
                                        <?php if($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_setting['user_edit'] == 1) || ($_SESSION['user_role'] == 'cashier' && $config_setting['user_edit'] == 1)) { ?>
                                            <a class="edit-btn" data-id="<?= $row['id'] ?>" data-username="<?= $row['username'] ?>" data-password="<?= $row['password'] ?>" data-user_role="<?= $row['user_role'] ?>" data-lastname="<?= $row['lastname'] ?>" data-firstname="<?= $row['firstname'] ?>" data-phone="<?= $row['phone'] ?>" data-age="<?= $row['age'] ?>" data-gender="<?= $row['gender'] ?>" data-bs-toggle="modal" data-bs-target="#EditUser">
                                                <i class="bi bi-pencil-square fs-5 text-title"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#cannotupdate">
                                                <i class="bi bi-pencil-square text-title fs-5"></i>
                                            </a>
                                        <?php } if($_SESSION['user_role'] == 'admin' || ($_SESSION['user_role'] == 'accounting' && $config_setting['user_delete'] == 1)) { ?>
                                            <a class="delete-btn" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#delete">
                                                <i class="bi bi-trash text-danger fs-5"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#cannotdelete">
                                                <i class="bi bi-trash text-danger fs-5"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php 
                                    $no++;
                                    } 
                                } else { ?>
                                    <tr><td colspan='5' class='text-center'>No products found.</td></tr>
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

    <!-- Add Products -->
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="user.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="number" class="form-control" id="age" name="age">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Gender">Gender</label>
                                    <select class="form-select" id="Gender" name="Gender" aria-label="Select Gender">
                                        <option selected>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="userrole">User Role</label>
                                    <select class="form-select" id="userrole" name="user_role" aria-label="Select User Role">
                                        <option selected>Select User Role</option>
                                        <option value="admin">admin</option>
                                        <option value="accounting">accounting</option>
                                        <option value="cashier">cashier</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="addUser" value="Submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="EditUser" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="update_id" id="update_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_lastname">Last Name</label>
                                    <input type="text" class="form-control" id="edit_lastname" name="lastname">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_firstname">First Name</label>
                                    <input type="text" class="form-control" id="edit_firstname" name="firstname">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_username">Username</label>
                                    <input type="text" class="form-control" id="edit_username" name="username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_password">Password</label>
                                    <input type="text" class="form-control" id="edit_password" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_phone">Phone</label>
                                    <input type="text" class="form-control" id="edit_phone" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_age">Age</label>
                                    <input type="text" class="form-control" id="edit_age" name="age">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_gender">Gender</label>
                                    <select class="form-select" id="edit_gender" name="gender" aria-label="Select Gender">
                                        <option selected>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_userrole">User Role</label>
                                    <select class="form-select" id="edit_userrole" name="user_role" aria-label="Select User Role">
                                        <option selected>Select User Role</option>
                                        <option value="admin">admin</option>
                                        <option value="accounting">accounting</option>
                                        <option value="cashier">cashier</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="updateUser" class="btn btn-primary">Update</button>
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

    <!-- Permission Delete Confirmation Modal -->
    <div class="modal fade" id="cannotdelete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Warning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span class="text-danger">You cannot delete this record</span> (Please contact the administrator.)
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Permission Update -->
    <div class="modal fade" id="cannotupdate" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Warning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span class="text-danger">You cannot update this record</span> (Please contact the administrator.)
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var username = $(this).data('username');
                var password = $(this).data('password');
                var user_role = $(this).data('user_role');
                var lastname = $(this).data('lastname');
                var firstname = $(this).data('firstname');
                var phone = $(this).data('phone');
                var age = $(this).data('age');
                var gender = $(this).data('gender');

                $('#update_id').val(id);
                $('#edit_username').val(username);
                $('#edit_password').val(password);
                $('#edit_userrole').val(user_role);
                $('#edit_lastname').val(lastname);
                $('#edit_firstname').val(firstname);
                $('#edit_phone').val(phone);
                $('#edit_age').val(age);
                $('#edit_gender').val(gender);
            });

            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });
        });
    </script>
</body>
</html>