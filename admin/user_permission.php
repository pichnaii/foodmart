<?php 
    include 'include/dbconnection.php';

    // Delete Product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("SELECT user_image FROM user_roles WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->bind_result($imagePath);
        $stmt->fetch();
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM user_roles WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
    
        if ($stmt->execute()) {
            if (!empty($imagePath)) {
                $filePath = "images/uploads/users/" . $imagePath;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $_SESSION['message'] = 'User role deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to delete User Role!';
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header('Location: user_permission.php');
        exit();
    }

    $user_roles = $conn->query("SELECT * FROM user_roles ORDER BY created_date DESC");
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
                        <a href="add_user_role.php" class="btn btn-primary mb-3">
                            <i class="fas fa-plus"></i> Add User Role
                        </a>
                        <h5 class="mb-0 fw-bold text-title">User Roles List</h5>
                    </div>
                    <?php if(isset($_SESSION['message'])){?>
                        <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show turn-off" role="alert">
                            <?=$_SESSION['message']?>
                            <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php } ?>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="bg-secondary text-light text-center">
                                    <th>No</th>
                                    <th>User Image</th>
                                    <th>Code</th>
                                    <th>User Role</th>
                                    <th>Description</th>
                                    <th>Created Date</th>
                                    <th>Updated Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-title">
                                <?php 
                                    if($user_roles->num_rows > 0) {
                                        $i = 1; 
                                        while($row = $user_roles->fetch_assoc()) { 
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $i++ ?></td>
                                            <td class="text-center"><?= !empty($row['user_image']) ? '<img src="images/uploads/users/'. $row['user_image'] .'" style="width:4rem;height:auto;">' : '<img src="images/uploads/no-image.png" style="width:3rem;height:auto;">';?></td>
                                            <td class="text-center"><?= $row['code'] ?></td>
                                            <td class="text-center"><?= $row['name'] ?></td>
                                            <td class="text-center"><?= $row['description'] ?></td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($row['created_date'])) ?></td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($row['updated_date'])) ?></td>
                                            <td class="text-center">
                                                <?php if($row['status'] == 1): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="permission.php" class="edit-btn">
                                                    <i class="bi bi-eye cursor-pointer fs-4 text-title"></i>
                                                </a>
                                                <a href="edit_user_role.php?id=<?= $row['id'] ?>" class="edit-btn">
                                                    <i class="bi bi-pencil-square cursor-pointer fs-4 text-title"></i>
                                                </a>
                                                <a class="delete-btn" data-bs-toggle="modal" data-bs-target="#deleteUnit" data-id="<?= $row['id'] ?>">
                                                    <i class="bi bi-trash text-danger cursor-pointer fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } } else { ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-danger">No User Roles Found</td>
                                        </tr>
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

    <!-- Delete Unit Confirmation -->
    <div class="modal fade" id="deleteUnit" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                    <small class="d-none mt-2 text-danger">*Your action cannot be reversed.</small>
                    <small class="d-block mt-2 text-danger">*សកម្មភាពរបស់អ្នកមិនអាចត្រឡប់ក្រោយបានទេ។</small>
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="deleteUnit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var date = $(this).data('created_date');
                var code = $(this).data('code');
                var name = $(this).data('name');
                var fullname = $(this).data('fullname');

                $('#edit_id').val(id);
                $('#edit_created_date').val(date);
                $('#edit_code').val(code);
                $('#edit_name').val(name);
                $('#edit_fullname').val(fullname);
            });

            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                $('#delete_id').val(id);
            });

            // Auto hide alert after 3 seconds
            setTimeout(function() {
                $('.turn-off').alert('close');
            }, 1500);
        });
    </script>
</body>
</html>