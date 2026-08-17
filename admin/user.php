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
                            <a href="add_user.php" class="btn btn-primary mb-3">
                                <i class="fas fa-plus"></i> Add User
                            </a>
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
                                        <a href="edit_user.php?id=<?= $row['id'] ?>" class="edit-btn">
                                            <i class="bi bi-pencil-square fs-5 text-title"></i>
                                        </a>
                                        <a class="delete-btn" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#delete">
                                            <i class="bi bi-trash text-danger fs-5 cursor-pointer"></i>
                                        </a>
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
            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });
        });
    </script>
</body>
</html>